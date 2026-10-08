<?php

namespace App\Modules\Cash\Controllers;

use App\Models\sucursales;
use App\services\caja\CajaDocumentosService;

use App\Core\Routing\Router;

/** Acciones HTTP del cierre de caja; las reglas permanecen en los servicios. */
class PrintCajaController{

    public static function printfacturacarta(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $id = self::obtenerIdDocumento();
    if(!$id)return;
    $datos = (new CajaDocumentosService())->prepararFacturaCarta($id, id_sucursal());
    if(!$datos){
      self::responderDocumentoNoEncontrado();
      return;
    }
    $router->render('admin/caja/printFacturaCarta', $datos + ['titulo'=>'Impresion factura', 'user'=>$_SESSION]);
  }

  /**
   * GET /printcotizacion?id={factura}.
   *
   * Es abierto desde ordenresumen.ts. CajaDocumentosService comparte el mismo
   * detalle validado de la factura y sus relaciones.
   */
  public static function printcotizacion(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $id = self::obtenerIdDocumento();
    if(!$id)return;
    $datos = (new CajaDocumentosService())->prepararCotizacion($id, id_sucursal());
    if(!$datos){
      self::responderDocumentoNoEncontrado();
      return;
    }
    $router->render('admin/caja/printcotizacion', $datos + ['titulo'=>'Impresion cotizacion', 'user'=>$_SESSION]);
  }

  /**
   * GET /printdetallecierre?id={cierre}.
   *
   * Es abierto desde cerrarcaja.ts y detallecierrecaja.ts. El servicio combina
   * el resumen financiero compartido con el encabezado de la sucursal.
   */
  public static function printdetallecierre(Router $router){
    isadmin();
    if(!tienePermiso('Habilitar modulo de caja')&&userPerfil()>3)return;
    $id = self::obtenerIdDocumento();
    if(!$id)return;
    $datos = (new CajaDocumentosService())->prepararDetalleCierre($id, id_sucursal());
    if(!$datos){
      self::responderDocumentoNoEncontrado();
      return;
    }
    $router->render('admin/caja/printdetallecierre', $datos + ['titulo'=>'detalle cierre Caja', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
  }

  /** Normaliza el parámetro id compartido por las tres rutas documentales. */
  private static function obtenerIdDocumento(): ?int{
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
    return $id === false ? null : (int)$id;
  }

  /** Devuelve una respuesta controlada cuando el documento no está en alcance. */
  private static function responderDocumentoNoEncontrado(): void{
    http_response_code(404);
    echo 'Documento no encontrado.';
  }

}