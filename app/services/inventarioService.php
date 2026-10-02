<?php 

namespace App\services;

use App\Models\ActiveRecord;
use App\Models\inventario\conversionunidades;
use App\Models\inventario\productos;
use App\Models\inventario\stockinsumossucursal;
use App\Models\inventario\stockproductossucursal;
use App\Models\inventario\subproductos;
use App\Models\sucursales;
use PhpOffice\PhpSpreadsheet\IOFactory;
use stdClass;

class inventarioService {
    
    public static function importarExcel($excel_temp){
        $alertas = [];
        $idsExcel = [];
        $productos = new productos();
        $conversion = new conversionunidades;
        $sucursales = sucursales::all();
        $getDB = productos::getDB();
        $getDB->begin_transaction();
        try {
            // Cargar el archivo Excel
            $spreadsheet = IOFactory::load($excel_temp);
            $worksheet = $spreadsheet->getActiveSheet();
            // Convertir a array
            $filas = $worksheet->toArray();
            
            // Procesar cada fila
            foreach($filas as $index => $fila){
                // Saltar encabezado
                if ($index == 0) continue;

                $v = self::filaVacia($fila);
                if($v['status']==false){
                    $alertas['error'][] = "error en fila $index ".$v['columna'];
                }
                
                $id = $fila[0];
                $idcategoria = $fila[1];
                $idunidadmedida = $fila[2];
                $nombre  = $fila[3];
                $impuesto = $fila[4];
                $tipoproducto  = $fila[5];  //0 = simple, 1 = compuesto
                $tipoproduccion = $fila[6]; //0 = inmediato, 1 = construccion
                $sku = $fila[7]??'';
                $preciopersonalizado = $fila[8];   //  0 = no,  1 = si
                $stock = $fila[9];
                $stockminimo = $fila[10];
                $precio_compra = $fila[11];
                $precio_venta  = $fila[12];
            
                if(isset($id))$idsExcel[] = $id;

                $producto = new stdClass;
                $producto->id = $id;
                $producto->idcategoria = $idcategoria;
                $producto->idunidadmedida = $idunidadmedida;
                $producto->nombre = $nombre;
                $producto->foto = '';
                $producto->impuesto = $impuesto;
                $producto->marca = '';
                $producto->tipoproducto = $tipoproducto;
                $producto->tipoproduccion = $tipoproduccion;
                $producto->sku = $sku;
                $producto->unidadmedida = '';
                $producto->preciopersonalizado = $preciopersonalizado;
                $producto->descripcion = '';
                $producto->peso = '';
                $producto->medidas = '';
                $producto->color = '';
                $producto->funcion = '';
                $producto->uso = '';
                $producto->fabricante = '';
                $producto->garantia = '';
                $producto->stock = $stock;
                $producto->cantidad = $stock;
                $producto->stockminimo = $stockminimo;
                $producto->categoria = '';
                $producto->merma = 0;
                $producto->rendimientoestandar = 1;
                $producto->precio_compra = $precio_compra;
                $producto->precio_venta = $precio_venta;
                //$producto->fecha_ingreso = '';
                $producto->estado = 1;
                $producto->visible = 1;

                $rows[] = $producto;
            }

            if(!empty($rows) && empty($alertas)){
                //PRODUCTOS EXISTENTES
                $productosExistentes = productos::IN_Where('id', $idsExcel, ['visible', 1]);
                //MAPERAR LOS ID EXISTENTES CON TRUE
                $mapId = [];
                foreach ($productosExistentes as $value)$mapId[$value->id] = true;

                $insertProductos = [];
                $rowsStock = [];
                $upsertProductos = [];
                $nuevosIds = [];
                $idsucursal = id_sucursal();

                foreach ($rows as $key => $value) {
                    $idReal = null;
                    // Primero, si trae ID, comprobar si existe
                    if($value->id !== "" && isset($mapId[$value->id]))$idReal = $value->id;

                    if($idReal != null){  //actualizar
                        $upsertProductos[] = $value;
                        //stockporsucursal
                        $rowsStock[] = "($idReal, $idsucursal, $value->stock, 0, 0, 0, 1)";
                    }else{  //insertar
                        $value->fecha_ingreso = date('Y-m-d H:i:s');
                        $insertProductos[] = $value;
                        
                    }
                }

                if(!empty($upsertProductos)) //actualizar productos
                    $u = $productos->updatemultiregobj($upsertProductos, ['idcategoria', 'idunidadmedida', 'nombre', 'impuesto', 'tipoproducto', 'tipoproduccion', 'sku', 'preciopersonalizado', 'stock', 'precio_compra', 'precio_venta']);
                
                if(!empty($insertProductos)){// insertar productos
                    $r = $productos->crear_varios_reg_arrayobj($insertProductos);
                    $lastId = $getDB->insert_id;
                    $cantidadInsertados = count($insertProductos);
                    $nuevosIds = range($lastId, $lastId + $cantidadInsertados - 1);

                    foreach($insertProductos as $i => $val){
                        $val->id = $nuevosIds[$i];
                        $idproducto = $nuevosIds[$i];
                        foreach($sucursales as $index => $value){
                            // Sucursal actual → stock Excel
                            if ($value->id == $idsucursal) {
                                $rowsStock[] = "($idproducto, $idsucursal, $val->stock, 0, 0, 0, 1)";
                                
                            } else {
                                // Otras sucursales → stock = 0
                                $rowsStock[] = "($idproducto, $value->id, 0, 0, 0, 0, 1)";
                            }
                        }
                    }

                    ///////  EQUIVALENCIAS DE UNIDAD DE MEDIDA  ////////////
                    $rowsEquivalencias = [];
                    foreach ($insertProductos as $producto) {
                        $equivs = $productos->equivalencias($producto->id, $producto->idunidadmedida);
                        if(!$equivs)throw new \Exception('Error en el id de la unidad de medida');
                        foreach ($equivs as $eq) {
                            $rowsEquivalencias[] = "({$eq->idproducto}, NULL, {$eq->idunidadmedidabase}, {$eq->idunidadmedidadestino}, '$eq->nombreunidadbase', '$eq->nombreunidaddestino', {$eq->factorconversion})";
                        }
                    }
                }


                //////  INSERTAR LAS EQUIVALENCIAS DE UNIDAD DE MEDIDA  ///////
                if (!empty($rowsEquivalencias)) {
                    $sqlEq = "INSERT INTO conversionunidades 
                        (idproducto, idsubproducto, idunidadmedidabase, idunidadmedidadestino, nombreunidadbase, nombreunidaddestino, factorconversion)
                        VALUES " . implode(',', $rowsEquivalencias);
                    conversionunidades::actualizarLibre($sqlEq);
                }


                //Traer todos los productos del stockporsucursal que se va a actualizar su stock
                $returnProductos = stockproductossucursal::IN_Where('productoid', array_keys($mapId), ['sucursalid', $idsucursal]);

                //STOCK POR SUCURSAL
                if (!empty($rowsStock)) {
                    $sqlStock = "INSERT INTO stockproductossucursal (productoid, sucursalid, stock, stockminimo, stockaux, promediostock, habilitarventa)
                    VALUES " . implode(",", $rowsStock) . 
                    "ON DUPLICATE KEY UPDATE stock = VALUES(stock)";
                    $k = stockproductossucursal::actualizarLibre($sqlStock);
                }
                
                //ACTUALIZAR MOVIMIENTO DE INVENTARIO
                count($upsertProductos)>0?stockService::upDate_movimientoProductos($upsertProductos, $returnProductos, 'ajuste', 'ajuste desde excel'):'';
                //movimiento de productos nuevos insertados
            }
            $getDB->commit();
            return $alertas;
        } catch (\Throwable $th) {
            //throw $th;
            $getDB->rollback();
            $alertas['error'][] = "Archvio de excel vacio o con errores en su contenido";
            $alertas['error'][] = $th->getMessage();
            return $alertas;
        }
    }


    public static function importarInsumosExcel($excel_temp){
        $alertas = [];
        $idsExcel = [];
        $subProductos = new subproductos();
        $sucursales = sucursales::all();
        $getDB = subproductos::getDB();
        $getDB->begin_transaction();
        try {
            // Cargar el archivo Excel
            $spreadsheet = IOFactory::load($excel_temp);
            $worksheet = $spreadsheet->getActiveSheet();
            // Convertir a array
            $filas = $worksheet->toArray();
            
            // Procesar cada fila
            foreach($filas as $index => $fila){
                // Saltar encabezado
                if ($index == 0) continue;

                $v = self::filaVacia($fila);
                if($v['status']==false){
                    $alertas['error'][] = "error en fila $index ".$v['columna'];
                }
                
                $id = $fila[0];
                $id_unidadmedida = $fila[1];
                $insumoprocesado = $fila[2];
                $nombre  = $fila[3];
                //$impuesto = $fila[4];
                $sku = $fila[4]??'';
                $stock = $fila[5];
                $stockminimo = $fila[6];
                $precio_compra = $fila[7];
            
                if(isset($id))$idsExcel[] = $id;

                $subProducto = new stdClass;
                $subProducto->id = $id;
                $subProducto->id_unidadmedida = $id_unidadmedida;
                $subProducto->insumoprocesado = $insumoprocesado;
                $subProducto->unidadmedida = '';
                $subProducto->nombre = $nombre;
                $subProducto->sku = $sku;
                $subProducto->proveedor = '';
                $subProducto->descripcion = '';
                $subProducto->medidas = '';
                $subProducto->color = '';
                $subProducto->uso = '';
                $subProducto->stock = $stock;
                $subProducto->stockminimo = $stockminimo;
                $subProducto->precio_compra = $precio_compra;
                $rows[] = $subProducto;
            }

            if(!empty($rows) && empty($alertas)){
                //SUBPRODUCTOS EXISTENTES
                $subProductosExistentes = subproductos::IN_Where('id', $idsExcel);
                //MAPERAR LOS ID EXISTENTES CON TRUE
                $mapId = [];
                foreach ($subProductosExistentes as $value)$mapId[$value->id] = true;

                $insertSubProductos = [];
                $rowsStock = [];
                $upsertSubProductos = [];
                $nuevosIds = [];
                $idsucursal = id_sucursal();

                foreach ($rows as $key => $value) {
                    $idReal = null;
                    // Primero, se trae ID, comprobar si existe
                    if($value->id !== "" && isset($mapId[$value->id]))$idReal = $value->id;

                    if($idReal != null){  //actualizar
                        $upsertSubProductos[] = $value;
                        //stockporsucursal
                        $rowsStock[] = "($idReal, $idsucursal, $value->stock, $value->stockminimo, 0, 0)";
                    }else{  //insertar
                        $value->fecha_ingreso = date('Y-m-d H:i:s');
                        $insertSubProductos[] = $value;
                    }
                }

                if(!empty($upsertSubProductos)) //actualizar subproductos
                    $u = $subProductos->updatemultiregobj($upsertSubProductos, ['id_unidadmedida', 'insumoprocesado', 'nombre', 'sku', 'stock', 'stockminimo', 'precio_compra']);
                
                if(!empty($insertSubProductos)){// insertar subproductos
                    $r = $subProductos->crear_varios_reg_arrayobj($insertSubProductos);
                    $lastId = $getDB->insert_id;
                    $cantidadInsertados = count($insertSubProductos);
                    $nuevosIds = range($lastId, $lastId + $cantidadInsertados - 1);

                    foreach($insertSubProductos as $i => $val){
                        $val->id = $nuevosIds[$i];
                        $idSubProducto = $nuevosIds[$i];
                        foreach($sucursales as $index => $value){
                            // Sucursal actual → stock Excel
                            if ($value->id == $idsucursal) {
                                $rowsStock[] = "($idSubProducto, $idsucursal, $val->stock, $val->stockminimo, 0, 0)";
                            } else {
                                // Otras sucursales → stock = 0
                                $rowsStock[] = "($idSubProducto, $value->id, 0, 0, 0, 0)";
                            }
                        }
                    }

                    ///////  EQUIVALENCIAS DE UNIDAD DE MEDIDA  ////////////
                    $rowsEquivalencias = [];
                    foreach ($insertSubProductos as $sub) {
                        $equivs = $subProductos->equivalencias($sub->id, $sub->id_unidadmedida);
                        if(!$equivs)throw new \Exception('Error en el id de la unidad de medida');
                        foreach ($equivs as $eq) {
                            $rowsEquivalencias[] = "(NULL, {$eq->idsubproducto}, {$eq->idunidadmedidabase}, {$eq->idunidadmedidadestino}, '$eq->nombreunidadbase', '$eq->nombreunidaddestino', {$eq->factorconversion})";
                        }
                    }
                }


                //////  INSERTAR LAS EQUIVALENCIAS DE UNIDAD DE MEDIDA  ///////
                if (!empty($rowsEquivalencias)) {
                    $sqlEq = "INSERT INTO conversionunidades 
                        (idproducto, idsubproducto, idunidadmedidabase, idunidadmedidadestino, nombreunidadbase, nombreunidaddestino, factorconversion)
                        VALUES " . implode(',', $rowsEquivalencias);
                    conversionunidades::actualizarLibre($sqlEq);
                }


                //Traer todos los subproductos del stockporsucursal que se va a actualizar su stock
                $returnSubProductos = stockinsumossucursal::IN_Where('subproductoid', array_keys($mapId), ['sucursalid', $idsucursal]);

                //STOCK POR SUCURSAL
                if (!empty($rowsStock)) {
                    $sqlStock = "INSERT INTO stockinsumossucursal (subproductoid, sucursalid, stock, stockminimo, stockaux, promediostock)
                    VALUES " . implode(",", $rowsStock) . 
                    "ON DUPLICATE KEY UPDATE stock = VALUES(stock)";
                    $k = stockinsumossucursal::actualizarLibre($sqlStock);
                }
                
                //ACTUALIZAR MOVIMIENTO DE INVENTARIO
                count($upsertSubProductos)>0?stockService::upDate_movimientoInsumos($upsertSubProductos, $returnSubProductos, 'ajuste', 'ajuste desde excel'):'';
                //movimiento de productos nuevos insertados
            }
            $getDB->commit();
            return $alertas;
        } catch (\Throwable $th) {
            //throw $th;
            $getDB->rollback();
            $alertas['error'][] = "Archvio de excel vacio o con errores en su contenido";
            $alertas['error'][] = $th->getMessage();
            return $alertas;
        }
    }


    private static function filaVacia($fila) {
        $s = "";
        $status = true;
        foreach($fila as $index => $celda) {
            if((!isset($celda) || trim($celda)=='') && $index>0 ) {
                $s .= "col: $index, ";
                $status = false;
            }elseif($index==0&&isset($celda)&&!ctype_digit($celda) || $index!=3&&$index!=0&&!is_numeric($celda)){
                $s .= "col: $index, ";
                $status = false;
            }
        }
        if(!$status)return ['status'=>false, 'columna'=>$s];
        return ['status'=>true];
    }


    public static function generarBarCode($date){
        try {
            //code...
        } catch (\Throwable $th) {
            //throw $th;
        }
        
    }


}
