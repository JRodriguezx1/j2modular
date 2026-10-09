<?php

namespace App\Modules\Cash\Controllers;

use App\Models\sucursales;
use App\Models\parametrizacion\config_local;
use App\services\caja\CajaConsultasService;
use App\services\caja\CajaMovimientosService;
use App\services\caja\CategoriasGastoService;

use App\Core\Routing\Router;


class IngresosGastosController{

    /** POST /admin/api/ingresosgastos. */
    public static function ingresoGastoCaja(Router $router): void{
        isadmin();
        if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
        $alertas = [];
        date_default_timezone_set('America/Bogota');

        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
        $comprobante = ['ruta'=>null, 'rutaAbsoluta'=>null, 'alertas'=>[]];
        if(($_POST['operacion'] ?? '') === 'gasto')
            $comprobante = self::guardarComprobanteGasto($_FILES['imgcomprobante'] ?? null);

        if($comprobante['alertas']){
            $alertas = ['error'=>$comprobante['alertas']];
        }else{
            $alertas = (new CajaMovimientosService())->registrarMovimiento($_POST, id_sucursal(), (int)$_SESSION['id'], $comprobante['ruta']);

            // El archivo acaba de crearse para esta solicitud. Si el comando no
            // se confirmó, se elimina para no dejar comprobantes huérfanos.
            if(isset($alertas['error']) && $comprobante['rutaAbsoluta'] && file_exists($comprobante['rutaAbsoluta']))
            unlink($comprobante['rutaAbsoluta']);
        }
        }

        $datosPanel = (new CajaConsultasService())->obtenerPanelCaja(id_sucursal(), (int)$_SESSION['perfil'], (int)$_SESSION['id']);
        $router->render('admin/caja/index', $datosPanel + ['titulo'=>'Caja', 'sucursal'=>nombreSucursal(), 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }

    
    private static function guardarComprobanteGasto(?array $archivo): array{
        if(!$archivo || (int)($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)
        return ['ruta'=>null, 'rutaAbsoluta'=>null, 'alertas'=>[]];
        if((int)($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK)
        return ['ruta'=>null, 'rutaAbsoluta'=>null, 'alertas'=>['No fue posible cargar el comprobante.']];
        if((int)($archivo['size'] ?? 0) > 31000000)
        return ['ruta'=>null, 'rutaAbsoluta'=>null, 'alertas'=>['El comprobante no puede superar los 31 MB.']];

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file((string)$archivo['tmp_name']);
        $extensiones = ['image/jpeg'=>'jpg', 'image/png'=>'png'];
        if(!isset($extensiones[$mime]))
        return ['ruta'=>null, 'rutaAbsoluta'=>null, 'alertas'=>['Seleccione una imagen en formato jpeg o png.']];

        $subdominio = preg_replace('/[^a-zA-Z0-9_-]/', '', explode('.', (string)($_SERVER['HTTP_HOST'] ?? 'cliente'))[0]) ?: 'cliente';
        $documentRoot = rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 2).'/public'), '/\\');
        $directorio = $documentRoot.'/build/img/'.$subdominio.'/comprobantes';
        if(!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio))
        return ['ruta'=>null, 'rutaAbsoluta'=>null, 'alertas'=>['No fue posible preparar la carpeta de comprobantes.']];

        $rutaRelativa = $subdominio.'/comprobantes/'.bin2hex(random_bytes(12)).'.'.$extensiones[$mime];
        $rutaAbsoluta = $documentRoot.'/build/img/'.$rutaRelativa;
        if(!move_uploaded_file((string)$archivo['tmp_name'], $rutaAbsoluta))
        return ['ruta'=>null, 'rutaAbsoluta'=>null, 'alertas'=>['No fue posible guardar el comprobante.']];

        return ['ruta'=>$rutaRelativa, 'rutaAbsoluta'=>$rutaAbsoluta, 'alertas'=>[]];
    }

    /**
   * GET|POST /admin/caja/categoriaGasto.
   *
   * GET muestra el catálogo; POST recibe el formulario de eliminación de
   * views/admin/caja/categoriagasto.php. Las reglas se delegan al servicio.
   */
  public static function categoriaGasto(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $alertas = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST' )
      $alertas = (new CategoriasGastoService())->eliminarCategoria($_POST);
    self::renderCategoriasGasto($router, $alertas);
  }

  /**
   * POST /admin/caja/crear_categoriaGasto.
   *
   * Es llamado por src/ts/caja/categoriasgastos.ts al confirmar el formulario
   * de creación. El controlador conserva HTTP y renderizado.
   */
  public static function crear_categoriaGasto(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $alertas = (new CategoriasGastoService())->crearCategoria($_POST);
    self::renderCategoriasGasto($router, $alertas);
  }


  public static function trasladosRetirosDinero(Router $router){
    isadmin();
    //if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $alertas = [];
    $router->render('admin/caja/trasladosRetiros', ['titulo'=>'Caja', 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }

  
  /**
   * POST /admin/caja/editarcategoriagasto.
   *
   * Es llamado por src/ts/caja/categoriasgastos.ts al confirmar una edición.
   * El servicio valida existencia, protección del catálogo base y duplicados.
   */
  public static function editarcategoriagasto(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $alertas = (new CategoriasGastoService())->editarCategoria($_POST);
    self::renderCategoriasGasto($router, $alertas);
  }

  /**
   * Render compartido por listado, creación, edición y eliminación.
   * Centraliza las variables requeridas por views/admin/caja/categoriagasto.php.
   */
  private static function renderCategoriasGasto(Router $router, array $alertas): void{
    $categoriasgastos = (new CategoriasGastoService())->listarCategorias();
    $router->render('admin/caja/categoriagasto', ['titulo'=>'Caja', 'conflocal'=>config_local::getParamGlobal(), 'categoriasgastos'=>$categoriasgastos, 'sucursal'=>nombreSucursal(), 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }

}