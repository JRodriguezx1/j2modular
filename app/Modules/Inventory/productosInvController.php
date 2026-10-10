<?php

namespace App\Controllers;

use App\Models\inventario\productos;
use App\Models\inventario\categorias;
Use App\Models\inventario\unidadesmedida;
use App\Models\inventario\stockproductossucursal;
use App\Models\sucursales;
use App\services\inventario\ProductosInventarioService;
use MVC\Router;  //namespace\clase

class almacencontrolador{

  public static function productos(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    //$productos = productos::all();
    $categorias = categorias::all();
    $unidadesmedida = unidadesmedida::all();
    $producto = new productos;

    $productos = productos::unJoinWhereArrayObj(stockproductossucursal::class, 'id', 'productoid', ['sucursalid'=>id_sucursal()]);
    foreach($productos as $value){
      $value->id = $value->ID;
      $value->nombrecategoria = categorias::find('id', $value->idcategoria)->nombre;
    }
    $router->render('admin/almacen/productos', ['titulo'=>'Almacen', 'productos'=>$productos, 'categorias'=>$categorias, 'unidadesmedida'=>$unidadesmedida, 'producto'=>$producto, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }

  public static function crear_producto(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $producto = new productos;

    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $resultado = (new ProductosInventarioService())->crearProducto($_POST, $_FILES, $_SERVER, (int)id_sucursal());
      $alertas = $resultado['alertas'];
      $producto = $resultado['producto'];
    }

    $productos = productos::unJoinWhereArrayObj(stockproductossucursal::class, 'id', 'productoid', ['sucursalid'=>id_sucursal()]);
    foreach($productos as $value){
      $value->id = $value->ID;
      $value->nombrecategoria = categorias::find('id', $value->idcategoria)->nombre;
    }
    $router->render('admin/almacen/productos', ['titulo'=>'Almacen', 'productos'=>$productos, 'categorias'=>categorias::all(), 'unidadesmedida'=>unidadesmedida::all(), 'producto'=>$producto, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  public static function actualizarproducto(){ //actualizar editar producto
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }
    $resultado = (new ProductosInventarioService())->actualizarProducto($_POST, $_FILES, $_SERVER, id_sucursal());
    echo json_encode($resultado);
    return;
  }


  public static function eliminarProducto(){
    if(!self::autorizarComandoInventario())return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      echo json_encode([]);
      return;
    }
    $resultado = (new ProductosInventarioService())->eliminarProducto($_POST, $_SERVER);
    echo json_encode($resultado);
    return;
  }

}