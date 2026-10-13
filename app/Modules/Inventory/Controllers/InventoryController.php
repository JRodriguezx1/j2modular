<?php

namespace App\Modules\Inventory\Controllers;

use App\Models\configuraciones\bancos;
use App\Models\inventario\productos;
use App\Models\inventario\subproductos;
use App\Models\inventario\productos_sub;
use App\Models\inventario\categorias;
Use App\Models\inventario\unidadesmedida;
use App\Models\configuraciones\caja;
use App\Models\inventario\grupos_insumos;
use App\Models\inventario\proveedores;
use App\Models\inventario\stockproductossucursal;
use App\Models\sucursales;
use App\services\exportService;
use App\services\inventario\CategoriasInventarioService;
use App\services\inventario\InsumosInventarioService;
use App\services\inventario\PanelInventarioService;
use App\services\inventario\ProductosInventarioService;
use App\services\inventario\UnidadesMedidaInventarioService;
use App\services\inventarioService;

use App\Core\Routing\Router;


class InventoryController{

  public static function index(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $panel = (new PanelInventarioService())->obtenerPanel(id_sucursal());
    $router->render('admin/almacen/index', array_merge(['titulo'=>'Almacen'], $panel, ['alertas'=>[], 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]));
  }


  public static function categorias(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $categorias = categorias::all();
    //$alertas = usuarios::getAlertas();
    $router->render('admin/almacen/categorias', ['titulo'=>'Almacen', 'categorias'=>$categorias, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }

  public static function crear_categoria(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST' )
      $alertas = (new CategoriasInventarioService())->crearCategoria($_POST);
    $categorias = categorias::all();
    $router->render('admin/almacen/categorias', ['titulo'=>'Almacen', 'categorias'=>$categorias, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }

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


  public static function subproductos(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $subproductos = subproductos::all();
    $unidadesmedida = unidadesmedida::all();
    $subproducto = new subproductos;
    $router->render('admin/almacen/subproductos', ['titulo'=>'Almacen', 'subproducto'=>$subproducto, 'subproductos'=>$subproductos, 'unidadesmedida'=>$unidadesmedida, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }

  public static function crear_subproducto(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $subproducto = new subproductos;

    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $resultado = (new InsumosInventarioService())->crearInsumo($_POST, (int)id_sucursal());
      $alertas = $resultado['alertas'];
      $subproducto = $resultado['subproducto'];
    }
    $sucursales = sucursales::all();
    $unidadesmedida = unidadesmedida::all();
    $subproductos = subproductos::all();
    $router->render('admin/almacen/subproductos', ['titulo'=>'Almacen', 'subproductos'=>$subproductos, 'unidadesmedida'=>$unidadesmedida, 'subproducto'=>$subproducto, 'alertas'=>$alertas, 'sucursales'=>$sucursales, 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }


  public static function componer(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $producto = productos::find('id', $_GET['id']);
    $subproductos = subproductos::all();
                                      // segunda tabla,   ON t1.id_subproducto = t2.id
    $subproductosenlazados = productos_sub::unJoinWhereArrayObj(subproductos::class, "id_subproducto", "id", ['productos_sub.id_producto'=>$producto->id]);
    $gi = array_column(grupos_insumos::whereArray(['activo'=>1]), 'nombre', 'id');
    foreach($subproductosenlazados as $value)
      $value->grupos_insumos = $gi[$value->grupos_insumos]??'Insumo por defecto';
    $router->render('admin/almacen/componer', ['titulo'=>'Almacen', 'producto'=>$producto, /*'productos'=>$productos,*/ 'subproductos'=>$subproductos, 'subproductosenlazados'=>$subproductosenlazados, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }


  public static function ajustarcostos(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $productos = productos::all();
    $subproductos = subproductos::all();
    $router->render('admin/almacen/ajustarcostos', ['titulo'=>'Almacen', 'productos'=>$productos, 'subproductos'=>$subproductos, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  public static function compras(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $categorias = categorias::all();
    $proveedores = proveedores::all();
    $cajas = caja::idregistros('idsucursalid', id_sucursal());
    $bancos = bancos::all();
    $totalitems = []; //array_merge($productos, $subproductos);
    $router->render('admin/almacen/compras', ['titulo'=>'Almacen', 'proveedores'=>$proveedores, 'totalitems'=>$totalitems, 'categorias'=>$categorias, 'cajas'=>$cajas, 'bancos'=>$bancos, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }
  

  public static function distribucion(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $categorias = categorias::all();
    $router->render('admin/almacen/distribucion', ['titulo'=>'Almacen', 'categorias'=>$categorias, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }

  public static function inventariar(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $categorias = categorias::all();
    $router->render('admin/almacen/inventariar', ['titulo'=>'Almacen', 'categorias'=>$categorias, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }

  public static function unidadesmedida(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST' )
      $alertas = (new UnidadesMedidaInventarioService())->eliminarUnidad($_POST);
    $unidadesmedida = unidadesmedida::all();
    $router->render('admin/almacen/unidadesmedida', ['titulo'=>'Almacen', 'unidadesmedida'=>$unidadesmedida, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }

  public static function crear_unidadmedida(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST' )
      $alertas = (new UnidadesMedidaInventarioService())->crearUnidad($_POST);

    $unidadesmedida = unidadesmedida::all();
    $router->render('admin/almacen/unidadesmedida', ['titulo'=>'Almacen', 'unidadesmedida'=>$unidadesmedida, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }

  public static function editarunidademedida(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST' )
      $alertas = (new UnidadesMedidaInventarioService())->actualizarUnidad($_POST);
    $unidadesmedida = unidadesmedida::all();
    $router->render('admin/almacen/unidadesmedida', ['titulo'=>'Almacen', 'unidadesmedida'=>$unidadesmedida, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION/*'negocio'=>negocio::get(1)*/]);
  }


  public static function downexcelproducts(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST' ) //para exportar a excel productos
      return;
    if(!isset($_POST['downexcel']))
      return; 
    $excelproductos = productos::formatExportExcel();
    exportService::exportproducts($excelproductos, 'excelproductos');
  }


  public static function uploadExcel(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $categorias = categorias::all();
    $unidadesmedida = unidadesmedida::all();
    $producto = new productos;

    if($_SERVER['REQUEST_METHOD'] === 'POST' && $_FILES['archivoexcel']['name']){ //para importar productos desde excel
        $url_temp = $_FILES["archivoexcel"]["tmp_name"];
        $extension = strtolower(pathinfo($_FILES['archivoexcel']['name'], PATHINFO_EXTENSION));
        // Validar extensión
        $extensiones_permitidas = ['xlsx', 'xls', 'csv'];
        if(in_array($extension, $extensiones_permitidas)){
          $alertas = inventarioService::importarExcel($url_temp);
          if(empty($alertas)){
            $alertas['exito'][] = "Carga de los productos con exito.";
          }
        }else{
          $alertas['error'][] = "Extension del archivo no valido";
        }
    }

    $productos = productos::unJoinWhereArrayObj(stockproductossucursal::class, 'id', 'productoid', ['sucursalid'=>id_sucursal()]);
    foreach($productos as $value){
      $value->id = $value->ID;
      $value->nombrecategoria = categorias::find('id', $value->idcategoria)->nombre;
    }
    $router->render('admin/almacen/productos', ['titulo'=>'Almacen', 'productos'=>$productos, 'categorias'=>$categorias, 'unidadesmedida'=>$unidadesmedida, 'producto'=>$producto, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  public static function uploadInsumosExcel(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $subproductos = subproductos::all();
    $unidadesmedida = unidadesmedida::all();
    $subproducto = new subproductos;

    if($_SERVER['REQUEST_METHOD'] === 'POST' && $_FILES['archivoexcel']['name']){ //para importar productos desde excel
        $url_temp = $_FILES["archivoexcel"]["tmp_name"];
        $extension = strtolower(pathinfo($_FILES['archivoexcel']['name'], PATHINFO_EXTENSION));
        $extensiones_permitidas = ['xlsx', 'xls', 'csv'];
        if(in_array($extension, $extensiones_permitidas)){
          $alertas = inventarioService::importarInsumosExcel($url_temp);
          if(empty($alertas))
            $alertas['exito'][] = "Extension del archivo no valido";
        }else{
          $alertas['error'][] = "Extension del archivo no valido";
        }
    }
    
    $router->render('admin/almacen/subproductos', ['titulo'=>'Almacen', 'subproducto'=>$subproducto, 'subproductos'=>$subproductos, 'unidadesmedida'=>$unidadesmedida, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }


  public static function downexcelinsumos(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    if($_SERVER['REQUEST_METHOD'] !== 'POST' )return;
    if(!isset($_POST['downexcel']))return; 
    $excelinsumos = subproductos::formatExportExcel();
    exportService::exportproducts($excelinsumos, 'excelinsumos.csv');
  }


  public static function cambioPrecios(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $productos = [];
    $productos = productos::all();
    $router->render('admin/almacen/cambioPrecios', ['titulo'=>'Almacen', 'productos'=>$productos, 'sucursales'=>sucursales::all()]);
  }


  public static function estadisticas(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $alertas = [];
    $router->render('admin/almacen/estadisticas', ['titulo'=>'Almacen', 'alertas'=>$alertas, 'sucursales'=>sucursales::all()]);
  }


  public static function productosParaFormulas(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $productos = productos::all();
    $router->render('admin/almacen/productosParaFormulas', ['titulo'=>'Almacen', 'productos'=>$productos, 'sucursales'=>sucursales::all()]);
  }


  public static function conversionUnidades(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de inventario')&&userPerfil()>3)return;
    $subproductos = subproductos::all();
    $router->render('admin/almacen/conversionUnidades', ['titulo'=>'Almacen', 'subproductos'=>$subproductos, 'sucursales'=>sucursales::all()]);
  }

}
