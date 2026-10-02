<?php

namespace App\services\inventario;

use App\Models\ActiveRecord;
use App\Models\inventario\categorias;
use App\Models\inventario\conversionunidades;
use App\Models\inventario\costosproductos;
use App\Models\inventario\precios_personalizados;
use App\Models\inventario\productos;
use App\Models\inventario\productos_sub;
use App\Models\inventario\stockproductossucursal;
use App\Models\inventario\unidadesmedida;
use App\Models\sucursales;
use RuntimeException;
use stdClass;
use Throwable;

final class ProductosInventarioService{

    //////////  crear producto  ////////////
    public function crearProducto(array $datos, array $archivos, array $servidor, int $sucursalId): array{
        $producto = new productos($datos);
        $imagen = $archivos['foto'] ?? ['name'=>'', 'size'=>0, 'type'=>'', 'tmp_name'=>''];
        $producto->validarimgproducto(['foto'=>$imagen]);
        $alertas = $producto->validar_nuevo_producto();
        if(!empty($alertas))return $this->respuesta($alertas, $producto);
        $generarSku = trim((string)$producto->sku) === '';
        if($generarSku)$producto->sku = null;

        $fotoNuevaAbsoluta = null;
        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        try{
            // La categoria se bloquea porque su contador sera actualizado.
            $categoria = categorias::findForUpdate('id', (int)$producto->idcategoria);
            if(!$categoria)throw new RuntimeException('La categoria seleccionada no existe.');

            // La unidad solamente se consulta para copiar su nombre al producto.
            $unidadmedida = unidadesmedida::find('id', (int)$producto->idunidadmedida);
            if(!$unidadmedida)throw new RuntimeException('La unidad de medida seleccionada no existe.');
            $producto->categoria = $categoria->nombre;
            $producto->unidadmedida = $unidadmedida->nombre;

            if(!empty($imagen['name']))[$producto->foto, $fotoNuevaAbsoluta] = $this->guardarImagen($imagen, $servidor);

            $resultadoProducto = $producto->crear_guardar();
            $producto->id = (int)$resultadoProducto[1];

            // El ID autoincremental permite generar un SKU interno unico sin competir por MAX(id).
            if($generarSku){
                $producto->sku = 'P'.str_pad((string)$producto->id, 6, '0', STR_PAD_LEFT);
                $producto->actualizar();
            }

            $categoria->totalproductos = productos::numreg_where('idcategoria', $categoria->id);
            if(!$categoria->actualizar())throw new RuntimeException('No fue posible actualizar la categoria.');

            $preciosPersonalizados = $this->construirPreciosPersonalizados($datos, $producto->id);
            if($preciosPersonalizados){
                $resultadoPrecios = (new precios_personalizados())->crear_varios_reg($preciosPersonalizados);
                if(!$resultadoPrecios[0])throw new RuntimeException('No fue posible crear los precios personalizados.');
            }

            $equivalencias = $producto->equivalencias($producto->id, (int)$producto->idunidadmedida);
            if(empty($equivalencias))throw new RuntimeException('No fue posible generar las conversiones del producto.');
            (new conversionunidades())->crear_varios_reg_arrayobj($equivalencias);

            $stocksSucursal = $this->construirStocksSucursal($producto, sucursales::all(), $sucursalId);
            $resultadoStocks = (new stockproductossucursal())->crear_varios_reg($stocksSucursal);
            if(!$resultadoStocks[0])throw new RuntimeException('No fue posible crear el inventario por sucursal.');

            (new costosproductos([
                'sucursalfk_id'=>$sucursalId,
                'productofk'=>$producto->id,
                'tipocosto'=>0,
                'precio_compra'=>$producto->precio_compra
            ]))->crear_guardar();

            $db->commit();
            return $this->respuesta(['exito'=>['Producto creado correctamente']], new stdClass());
        }catch(Throwable $error){
            $db->rollback();
            $this->eliminarImagenNueva($fotoNuevaAbsoluta);
            error_log('Error al crear producto: '.$error->getMessage());
            $mensaje = str_contains($error->getMessage(), 'Duplicate entry')
                ? 'Ya existe un producto con el mismo nombre o SKU'
                : 'Error al crear producto, verifica los campos e intenta nuevamente';
            return $this->respuesta(['error'=>[$mensaje]], $producto);
        }
    }

    private function construirPreciosPersonalizados(array $datos, int $productoId): array{
        $preciosPersonalizados = [];
        foreach(($datos['nuevosprecios'] ?? []) as $precio)
            if(is_numeric($precio) && (float)$precio > 0){
                $preciosPersonalizados[] = [
                    'idproductoid'=>$productoId,
                    'precio'=>$precio,
                    'estado'=>1
                ];
            }
        return $preciosPersonalizados;
    }

    private function construirStocksSucursal(productos $producto, array $sucursales, int $sucursalId): array{
        if(empty($sucursales))throw new RuntimeException('No existen sucursales para configurar el inventario.');

        $stocks = [];
        $sucursalActualEncontrada = false;
        foreach($sucursales as $sucursal){
            $esSucursalActual = (int)$sucursal->id === $sucursalId;
            if($esSucursalActual)$sucursalActualEncontrada = true;
            $stocks[] = [
                'productoid'=>$producto->id,
                'sucursalid'=>$sucursal->id,
                'stock'=>$esSucursalActual ? $producto->stock : 0,
                'stockminimo'=>$esSucursalActual ? $producto->stockminimo : 0,
                'stockaux'=>0,
                'habilitarventa'=>1,
                'promediostock'=>1
            ];
        }
        if(!$sucursalActualEncontrada)throw new RuntimeException('La sucursal actual no esta configurada.');
        return $stocks;
    }

    private function guardarImagen(array $imagen, array $servidor): array{
        $documentRoot = rtrim((string)($servidor['DOCUMENT_ROOT'] ?? ''), '/\\');
        $host = (string)($servidor['HTTP_HOST'] ?? '');
        if($documentRoot === '' || $host === '')
            throw new RuntimeException('No fue posible determinar el directorio de imagenes.');

        $subdominio = explode('.', $host)[0];
        $directorioFotos = $documentRoot.'/build/img/'.$subdominio.'/productos';
        if(!is_dir($directorioFotos) && !mkdir($directorioFotos, 0755, true))
            throw new RuntimeException('No fue posible preparar el directorio de imagenes.');

        $foto = $subdominio.'/productos/'.uniqid().basename((string)$imagen['name']);
        $rutaAbsoluta = $documentRoot.'/build/img/'.$foto;
        if(!move_uploaded_file((string)$imagen['tmp_name'], $rutaAbsoluta))
            throw new RuntimeException('No fue posible guardar la imagen.');
        return [$foto, $rutaAbsoluta];
    }

    private function eliminarImagenNueva(?string $rutaImagen): void{
        if(!$rutaImagen || !is_file($rutaImagen))return;
        if(!unlink($rutaImagen)) error_log('No fue posible eliminar la imagen del producto no creado: '.$rutaImagen);
    }

    private function respuesta(array $alertas, object $producto): array{
        return ['alertas'=>$alertas, 'producto'=>$producto];
    }


    //////////// actualizar producto  ///////////////
    public function actualizarProducto(array $datos, array $archivos, array $servidor, int $sucursalId): array{
        $productoId = (int)($datos['id'] ?? 0);
        $idsPreciosConservar = json_decode($datos['idprecionsadicionales'] ?? '[]', true);
        $preciosFront = json_decode($datos['nuevosprecios'] ?? '[]');
        if($productoId <= 0 || !is_array($idsPreciosConservar) || !is_array($preciosFront))
            return ['error'=>['Error, intenta nuevamente']];

        $hayNuevaImagen = isset($archivos['foto'])
            && ($archivos['foto']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
            && !empty($archivos['foto']['name']);
        if($hayNuevaImagen && ($archivos['foto']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK)
            return ['error'=>['No fue posible recibir la imagen']];

        $productoValidar = new productos($datos);
        if($hayNuevaImagen)$productoValidar->validarimgproducto($archivos);
        $alertas = $productoValidar->validar_nuevo_producto();
        if(!empty($alertas))return $alertas;

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        $fotoAnterior = '';
        $fotoNuevaAbsoluta = null;

        try{
            $producto = productos::findForUpdate('id', $productoId);
            if(!$producto)throw new RuntimeException('El producto no existe.');

            $tipoAnterior = (int)$producto->tipoproducto;
            $unidadAnterior = (int)$producto->idunidadmedida;
            $categoriaAnterior = (int)$producto->idcategoria;
            $fotoAnterior = (string)$producto->foto;
            $producto->compara_objetobd_post($datos);

            $categoriasProducto = $this->bloquearCategorias($categoriaAnterior, (int)$producto->idcategoria);
            $categoriasPorId = [];
            foreach($categoriasProducto as $categoria)
                $categoriasPorId[(int)$categoria->id] = $categoria;
            $producto->categoria = $categoriasPorId[(int)$producto->idcategoria]->nombre;

            if($hayNuevaImagen)
                [$producto->foto, $fotoNuevaAbsoluta] = $this->guardarImagen($archivos['foto'], $servidor);

            // Al pasar a compuesto, su costo unitario depende de la receta y el rendimiento.
            if($tipoAnterior === 0 && (int)$producto->tipoproducto === 1){
                $rendimiento = (float)$producto->rendimientoestandar;
                if($rendimiento <= 0)
                    throw new RuntimeException('El rendimiento estandar debe ser mayor que cero.');
                $costoFormula = (float)(productos_sub::sumcolum('id_producto', $producto->id, 'costo') ?? 0);
                $producto->precio_compra = $costoFormula / $rendimiento;
            }

            $producto->actualizar();
            (new costosproductos(['sucursalfk_id'=>$sucursalId, 'productofk'=>$producto->id, 'tipocosto'=>0, 'precio_compra'=>$producto->precio_compra]))->crear_guardar();

            $this->sincronizarPreciosPersonalizados((int)$producto->id, $idsPreciosConservar, $preciosFront);

            $stockProducto = stockproductossucursal::uniquewhereArrayForUpdate(['productoid'=>$producto->id, 'sucursalid'=>$sucursalId]);
            if(!$stockProducto)throw new RuntimeException('El producto no tiene inventario configurado en esta sucursal.');
            $stockProducto->stockminimo = $producto->stockminimo;
            $stockProducto->actualizar();

            $this->sincronizarConversiones($producto, $unidadAnterior);
            if($categoriaAnterior !== (int)$producto->idcategoria)
                foreach($categoriasProducto as $categoria){
                    $categoria->totalproductos = productos::numreg_where('idcategoria', $categoria->id);
                    $categoria->actualizar();
                }

            $producto->preciosadicionales = precios_personalizados::idregistros('idproductoid', $producto->id);
            $db->commit();
        }catch(Throwable $error){
            $db->rollback();
            if($fotoNuevaAbsoluta && is_file($fotoNuevaAbsoluta))unlink($fotoNuevaAbsoluta);
            error_log('Error al actualizar producto: '.$error->getMessage());
            return ['error'=>['Error, intenta nuevamente']];
        }

        // El archivo anterior se elimina solamente despues de confirmar la transaccion.
        if($hayNuevaImagen && $fotoAnterior)$this->eliminarImagen($fotoAnterior, $servidor);
        return ['exito'=>['Datos del producto actualizados'], 'producto'=>[$producto]];
    }


    private function bloquearCategorias(int $categoriaAnterior, int $categoriaNueva): array{
        $idsCategorias = array_values(array_unique([$categoriaAnterior, $categoriaNueva]));
        $categoriasProducto = categorias::findManyForUpdate($idsCategorias);
        if(count($categoriasProducto) !== count($idsCategorias))
            throw new RuntimeException('La categoria seleccionada no existe.');
        return $categoriasProducto;
    }

    /**
     * La pertenencia de los IDs recibidos al producto se validara posteriormente.
     */
    private function sincronizarPreciosPersonalizados(int $productoId, array $idsConservar, array $preciosFront): void{
        $preciosDB = precios_personalizados::idregistrosForUpdate('idproductoid', $productoId);
        $idsConservar = array_map('intval', $idsConservar);
        //Eliminar precios adicionales, si los ids de DB no estan en los nuevos ids, eliminar
        $idsEliminar = [];
        foreach($preciosDB as $precio)
            if(!in_array((int)$precio->id, $idsConservar, true))
                $idsEliminar[] = (int)$precio->id;

        //nuevos precios adicionales bienen sin id
        $preciosCrear = [];
        $preciosActualizar = [];
        foreach($preciosFront as $precio){
            $precio->precio = (float)($precio->precio ?? 0);
            if(!isset($precio->id)){
                $precio->idproductoid = $productoId;
                $precio->estado = $precio->estado ?? 1;
                $preciosCrear[] = $precio;
            }else{
                // Pendiente: comprobar que cada ID recibido pertenezca al producto.
                $precio->id = (int)$precio->id;
                $preciosActualizar[] = $precio;
            }
        }

        if($idsEliminar && !precios_personalizados::eliminar_idregistros('id', $idsEliminar))
            throw new RuntimeException('No fue posible eliminar precios personalizados.');
        if($preciosCrear)
            (new precios_personalizados())->crear_varios_reg_arrayobj($preciosCrear);
        if($preciosActualizar && !precios_personalizados::updatemultiregobj($preciosActualizar, ['precio']))
            throw new RuntimeException('No fue posible actualizar precios personalizados.');
    }


    private function sincronizarConversiones(productos $producto, int $unidadAnterior): void{
        if($unidadAnterior === (int)$producto->idunidadmedida)return;

        $conversionesDB = conversionunidades::idregistrosForUpdate('idproducto', (int)$producto->id);
        $equivalencias = $producto->equivalencias((int)$producto->id, (int)$producto->idunidadmedida);
        if(empty($equivalencias))
            throw new RuntimeException('No fue posible generar las conversiones de la nueva unidad.');
        if($conversionesDB && !conversionunidades::eliminar_idregistros('idproducto', [$producto->id]))
            throw new RuntimeException('No fue posible eliminar las conversiones anteriores.');

        (new conversionunidades())->crear_varios_reg_arrayobj($equivalencias);
    }


    /*private function guardarNuevaImagen(array $imagen, array $servidor): array{
        $documentRoot = rtrim((string)($servidor['DOCUMENT_ROOT'] ?? ''), '/\\');
        $host = (string)($servidor['HTTP_HOST'] ?? '');
        if($documentRoot === '' || $host === '')throw new RuntimeException('No fue posible determinar el directorio de imagenes.');

        $subdominio = explode('.', $host)[0];
        $directorioFotos = $documentRoot.'/build/img/'.$subdominio.'/productos';
        if(!is_dir($directorioFotos) && !mkdir($directorioFotos, 0755, true))
            throw new RuntimeException('No fue posible preparar el directorio de imagenes.');

        $foto = $subdominio.'/productos/'.uniqid().basename((string)$imagen['name']);
        $rutaAbsoluta = $documentRoot.'/build/img/'.$foto;
        if(!move_uploaded_file((string)$imagen['tmp_name'], $rutaAbsoluta))
            throw new RuntimeException('No fue posible guardar la imagen.');
        return [$foto, $rutaAbsoluta];
    }*/


    /*private function eliminarImagenAnterior(string $foto, array $servidor): void{
        $documentRoot = rtrim((string)($servidor['DOCUMENT_ROOT'] ?? ''), '/\\');
        if($documentRoot === '')return;

        $directorioImagenes = realpath($documentRoot.'/build/img');
        $rutaImagen = realpath($documentRoot.'/build/img/'.$foto);
        if(!$directorioImagenes || !$rutaImagen)return;

        $prefijoPermitido = rtrim($directorioImagenes, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if(!str_starts_with($rutaImagen, $prefijoPermitido))return;
        if(is_file($rutaImagen) && !unlink($rutaImagen))
            error_log('No fue posible eliminar la imagen anterior del producto: '.$rutaImagen);
    }*/


    /////////// Eliminar producto  //////////////
    public function eliminarProducto(array $datos, array $servidor): array{
        $productoId = (int)($datos['id'] ?? 0);
        if($productoId <= 0)return ['error'=>['Error, intenta nuevamente']];

        $db = ActiveRecord::getDB();
        $db->begin_transaction();
        $foto = '';

        try{
            // Mantiene el mismo orden de bloqueo usado al actualizar: producto y categoria.
            $producto = productos::findForUpdate('id', $productoId);
            if(!$producto)throw new RuntimeException('El producto no existe.');

            $categoria = categorias::findForUpdate('id', (int)$producto->idcategoria);
            if(!$categoria)throw new RuntimeException('La categoria del producto no existe.');

            $foto = (string)$producto->foto;
            $producto->eliminar_registro();
            // Recalcula el total para no depender de un contador que podria estar desactualizado.
            $categoria->totalproductos = productos::numreg_where('idcategoria', $categoria->id);
            $categoria->actualizar();
            $db->commit();
        }catch(Throwable $error){
            $db->rollback();
            return ['error'=>['Error durante el proceso, intenta nuevamente.']];
        }

        // La imagen no se elimina hasta confirmar la eliminacion en la base de datos.
        if($foto)$this->eliminarImagen($foto, $servidor);
        return ['exito'=>['Producto eliminado.']];
    }

    private function eliminarImagen(string $foto, array $servidor): void{
        $documentRoot = rtrim((string)($servidor['DOCUMENT_ROOT'] ?? ''), '/\\');
        if($documentRoot === '')return;

        $directorioImagenes = realpath($documentRoot.'/build/img');
        $rutaImagen = realpath($documentRoot.'/build/img/'.$foto);
        if(!$directorioImagenes || !$rutaImagen)return;

        $prefijoPermitido = rtrim($directorioImagenes, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if(!str_starts_with($rutaImagen, $prefijoPermitido))return;
        if(is_file($rutaImagen) && !unlink($rutaImagen))
            error_log('No fue posible eliminar la imagen del producto: '.$rutaImagen);
    }

}