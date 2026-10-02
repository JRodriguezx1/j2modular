# Inventario: respaldo de registrarCompra()

Este archivo conserva como referencia la implementación transaccional de `almacencontrolador::registrarCompra()` anterior a su futura delegación al servicio.

> Este código es documentación y no se ejecuta. El método activo permanece en `app/Controllers/almacencontrolador.php`.

```php
  public static function registrarCompra(){  //asociar un o unos subproductos a un producto principal
    date_default_timezone_set('America/Bogota');
    $alertas = [];

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode($alertas);
      return;
    }

    $db = ActiveRecord::getDB();
    $transaccionIniciada = false;

    try{
      $compra = new compras($_POST);
      $carrito = json_decode($_POST['carrito'] ?? '');
      if(!is_array($carrito) || empty($carrito))throw new \DomainException('Debe agregar al menos un item para comprar.');

      $resultArray = array_reduce($carrito, function($acumulador, $objeto){
        $objeto->id = $objeto->iditem;
        if($objeto->tipo == 0){
          $objeto->productofk = $objeto->iditem;
          $acumulador['productos'][] = $objeto;
          $acumulador['soloIdproductos'][] = $objeto->id;
        }else{
          $objeto->idsubproductoid = $objeto->iditem;
          $acumulador['subproductos'][] = $objeto;
          $acumulador['soloIdinsumos'][] = $objeto->id;
        }
        return $acumulador;
      }, ['productos'=>[], 'subproductos'=>[], 'soloIdproductos'=>[], 'soloIdinsumos'=>[]]);

      ////// Obtengo los registros de la tabla productos_sub a actualizar su costo de compra por su id_subproducto, si el subproducto se repite lo trae n veces
      $itemsProSub = productos_sub::paginarwhere('', '', 'id_subproducto', $resultArray['soloIdinsumos']);
       ////////  mapeo Optimizado de tipo O(A+B)
      $mapCostoSub = array_column($resultArray['subproductos'], 'precio_compra', 'id');

      $idsProductosCompuestos = [];
      foreach($itemsProSub as $itemProSub){
        if(!array_key_exists($itemProSub->id_subproducto, $mapCostoSub))
          throw new \RuntimeException('No fue posible relacionar el costo de un insumo.');
        $itemProSub->costo = $mapCostoSub[$itemProSub->id_subproducto];
        $idsProductosCompuestos[] = (int)$itemProSub->id_producto;
      }

      $compra->idusuario = $_SESSION['id'];
      $compra->nombreusuario = $_SESSION['nombre'];
      $compra->id_sucursal_id = id_sucursal();
      $alertas = $compra->validar();
      if(!empty($alertas)){
        echo json_encode($alertas);
        return;
      }

      if(!$db->begin_transaction())throw new \RuntimeException('No fue posible iniciar la transaccion de la compra.');
      $transaccionIniciada = true;

      // La caja se bloquea primero para serializar compras y aperturas de cierre concurrentes.
      $cajaOrigen = caja::findForUpdate('id', (int)$compra->idorigencaja);
      if(!$cajaOrigen)throw new \RuntimeException('La caja seleccionada no existe.');

      if(!empty($itemsProSub)){
        if(!productos_sub::actualizar_costos_de_prosub($itemsProSub, ['costo']))throw new \RuntimeException('No fue posible actualizar los costos de los insumos en las formulas.');

        $in = implode(', ', array_unique($idsProductosCompuestos));
        $sql = "SELECT SUM(costo)/productos.rendimientoestandar AS precio_compra, id_producto as productofk, id_producto AS id, 1 as tipocosto FROM productos_sub JOIN productos ON productos_sub.id_producto = productos.id
                WHERE id_producto IN ($in) GROUP BY id_producto;";
        $costos = productos_sub::camposJoinObj($sql);

        if(!empty($costos)){
          if(!productos::updatemultiregobj($costos, ['precio_compra']))throw new \RuntimeException('No fue posible actualizar el costo de los productos compuestos.');
          $resultadoCostos = (new costosproductos())->crear_varios_reg_arrayobj($costos);
          if(empty($resultadoCostos[0]))throw new \RuntimeException('No fue posible registrar los costos de los productos compuestos.');
        }
      }

      $resultadoCompra = $compra->crear_guardar();
      if(empty($resultadoCompra[0]))throw new \RuntimeException('No fue posible registrar la compra.');
      $idCompra = (int)$resultadoCompra[1];

      foreach($carrito as $item)$item->idcompra = $idCompra;
      $resultadoDetalle = (new detallecompra())->crear_varios_reg_arrayobj($carrito);
      if(empty($resultadoDetalle[0]))throw new \RuntimeException('No fue posible registrar el detalle de la compra.');

      if(!empty($resultArray['productos'])){
        if(!productos::camposaddinv($resultArray['productos'], ['stock', 'precio_compra']))
          throw new \RuntimeException('No fue posible actualizar los productos.');
        if(!stockproductossucursal::addinv1condicion($resultArray['productos'], 'stock', 'productoid', 'sucursalid = '.id_sucursal()))
          throw new \RuntimeException('No fue posible actualizar el stock de productos de la sucursal.');

        $query = "SELECT * FROM stockproductossucursal WHERE productoid IN(".join(', ', $resultArray['soloIdproductos']).") AND sucursalid = ".id_sucursal().";";
        $returnProductos = stockproductossucursal::camposJoinObj($query);
        if(!stockService::upStock_movimientoProductos($resultArray['productos'], $returnProductos, 'compra', 'ingreso de unidades por compra'))
          throw new \RuntimeException('No fue posible registrar los movimientos de los productos.');

        $resultadoCostos = (new costosproductos())->crear_varios_reg_arrayobj($resultArray['productos']);
        if(empty($resultadoCostos[0]))throw new \RuntimeException('No fue posible registrar los costos de los productos.');
      }

      if(!empty($resultArray['subproductos'])){
        if(!subproductos::camposaddinv($resultArray['subproductos'], ['stock', 'precio_compra']))
          throw new \RuntimeException('No fue posible actualizar los insumos.');
        if(!stockinsumossucursal::addinv1condicion($resultArray['subproductos'], 'stock', 'subproductoid', 'sucursalid = '.id_sucursal()))
          throw new \RuntimeException('No fue posible actualizar el stock de insumos de la sucursal.');

        $query = "SELECT * FROM stockinsumossucursal WHERE subproductoid IN(".join(', ', $resultArray['soloIdinsumos']).") AND sucursalid = ".id_sucursal().";";
        $returnInsumos = stockinsumossucursal::camposJoinObj($query);
        if(!stockService::upStock_movimientoInsumos($resultArray['subproductos'], $returnInsumos, 'compra', 'ingreso de unidades por compra'))
          throw new \RuntimeException('No fue posible registrar los movimientos de los insumos.');

        $resultadoCostos = (new costosinsumos())->crear_varios_reg_arrayobj($resultArray['subproductos']);
        if(empty($resultadoCostos[0]))
          throw new \RuntimeException('No fue posible registrar los costos de los insumos.');
      }

      $ultimocierre = cierrescajas::uniquewhereArray(['estado'=>0, 'idcaja'=>(int)$compra->idorigencaja, 'idsucursal_id'=>id_sucursal()]);

      if($ultimocierre){
        $ultimocierre = cierrescajas::findForUpdate('id', (int)$ultimocierre->id);
        if(!$ultimocierre || (int)$ultimocierre->estado !== 0)
          throw new \RuntimeException('El cierre de caja ya no se encuentra abierto.');
      }else{
        $ultimocierre = new cierrescajas(['idcaja'=>(int)$compra->idorigencaja, 'nombrecaja'=>$cajaOrigen->nombre, 'estado'=>0, 'idsucursal_id'=>id_sucursal(), 'id_usuario'=>(int)$compra->idusuario, 'nombreusuario'=>(string)$compra->nombreusuario]);
        $resultadoCierre = $ultimocierre->crear_guardar();
        if(empty($resultadoCierre[0]))
          throw new \RuntimeException('No fue posible abrir el cierre de caja.');
        $ultimocierre->id = (int)$resultadoCierre[1];
      }

      $ingresoGasto = new gastos(['idg_usuario'=>(int)$compra->idusuario, 'id_compra'=>$idCompra, 'idg_caja'=>(int)$compra->idorigencaja, 'idg_cierrecaja'=>(int)$ultimocierre->id, 'idcategoriagastos'=>1, 'operacion'=>'compra', 'valor'=>$compra->valortotal]);

      if((int)$compra->origenpago === 0){
        $ultimocierre->gastoscaja = (float)$ultimocierre->gastoscaja + (float)$ingresoGasto->valor;
      }else{
        $ingresoGasto->id_banco = $compra->idorigenbanco;
        $ingresoGasto->tipo_origen = 1;
        $ultimocierre->gastosbanco = (float)$ultimocierre->gastosbanco + (float)$ingresoGasto->valor;
      }

      $erroresGasto = $ingresoGasto->validar();
      if(!empty($erroresGasto))throw new \DomainException($erroresGasto['error'][0] ?? 'Los datos del gasto de compra no son validos.');

      $resultadoGasto = $ingresoGasto->crear_guardar();
      if(empty($resultadoGasto[0]))throw new \RuntimeException('No fue posible registrar el gasto de la compra.');
      if(!$ultimocierre->actualizar())throw new \RuntimeException('No fue posible actualizar los gastos del cierre de caja.');

      if(!$db->commit())throw new \RuntimeException('No fue posible confirmar la compra.');
      $transaccionIniciada = false;

      $alertas = ['exito'=>['Compra realizada con exito.'], 'idcompra'=>$idCompra];
    }catch(\DomainException $error){
      if($transaccionIniciada)$db->rollback();
      $alertas = ['error'=>[$error->getMessage()]];
    }catch(\Throwable $error){
      if($transaccionIniciada)$db->rollback();
      error_log('Error al registrar compra: '.$error->getMessage());
      $alertas = ['error'=>['Hubo un error, intentalo nuevamente']];
    }

    echo json_encode($alertas);
  }
```
