<?php
//$dias = facturacion::inner_join('SELECT COUNT(id) AS servicios, fecha_pago, SUM(total) AS totaldia FROM facturacion GROUP BY fecha_pago ORDER BY COUNT(id) DESC;');
namespace App\Controllers;

use App\Models\ActiveRecord;
use App\Models\caja\cierrescajas;
use App\Models\caja\ingresoscajas;
use App\Models\clientes\departments;
use App\Models\compras;
use App\Models\configuraciones\caja;
use App\Models\configuraciones\consecutivos;
use App\Models\configuraciones\emisores;
use App\Models\configuraciones\usuarios;
use App\Models\detallecompra;
use App\Models\felectronicas\adquirientes;
use App\Models\felectronicas\facturas_electronicas;
use App\Models\gastos;
use App\Models\inventario\productos;
use App\Models\inventario\proveedores;
use App\Models\inventario\stockinsumossucursal;
use App\Models\inventario\stockproductossucursal;
use App\Models\inventario\subproductos;
use App\Models\sucursales;
use App\Models\ventas\facturas;
use App\Repositories\creditos\creditosRepository;
use App\Repositories\creditos\cuotasRepository;
use App\services\caja\CajaReportesService;
use App\services\whatsAppService;
use MVC\Router;  //namespace\clase
 
class reportescontrolador{

    public static function index(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];

        $router->render('admin/reportes/index', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    ///////////////////////// Reportes ///////////////////////////////////
    public static function ventasgenerales(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];

        $router->render('admin/reportes/ventas/ventasgenerales', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function ventasxtransaccion(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];

        $router->render('admin/reportes/ventas/ventasxtransaccion', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function vistaVentasxcliente(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];

        $router->render('admin/reportes/ventas/ventasxcliente', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function ventaProductosUsuarios(Router $router){
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];
        $router->render('admin/reportes/ventas/ventaProductosUsuarios', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function reporteEmisores(Router $router){
      isadmin();
      if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
      $emisores = emisores::whereArray(['idsucursal'=>id_sucursal(), 'estado'=>1]);
      $router->render('admin/reportes/ventas/reporteEmisores', ['titulo'=>'Reportes', 'emisores'=>$emisores, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }

    public static function facturaspagas(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];

        $router->render('admin/reportes/facturas/facturaspagas', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }


    public static function remisiones(Router $router){
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $router->render('admin/reportes/facturas/remisiones', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }


    public static function creditos(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];
        $router->render('admin/reportes/ventas/estadosCreditos', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function cuotasCreditos(Router $router){
        //session_start();
        isadmin();
        $alertas = [];
        $idsucursal = id_sucursal();
        /*$clientes = clientes::all();
        $mediospago = mediospago::whereArray(['estado'=>1]);
        $cajas = caja::whereArray(['idsucursalid'=>$idsucursal, 'estado'=>1]);
        $consecutivos = consecutivos::whereArray(['id_sucursalid'=>$idsucursal, 'estado'=>1]);
        $conflocal = config_local::getParamCaja();*/
        $router->render('admin/reportes/facturas/cuotascreditos', ['titulo'=>'Reportes', 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }

    public static function creditosFinalizados(Router $router){
        //session_start();
        isadmin();
        $alertas = [];
        $idsucursal = id_sucursal();
        $creditos = new creditosRepository();
        $creditosFinalizados = $creditos->unJoinWhereArrayObj('clientes', 'cliente_id', 'id', ['id_fksucursal'=>id_sucursal(), 'idestadocreditos'=>1]);
        $router->render('admin/reportes/facturas/creditosfinalizados', ['titulo'=>'Reportes', 'creditosFinalizados'=>$creditosFinalizados, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }

    public static function creditosAnulados(Router $router){
        //session_start();
        isadmin();
        $alertas = [];
        $idsucursal = id_sucursal();
        $creditos = new creditosRepository();
        $creditosAnulados = $creditos->unJoinWhereArrayObj('clientes', 'cliente_id', 'id', ['id_fksucursal'=>id_sucursal(), 'idestadocreditos'=>3]);
        $router->render('admin/reportes/facturas/creditosanulados', ['titulo'=>'Reportes', 'creditosAnulados'=>$creditosAnulados, 'alertas'=>$alertas, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION]);
    }
    
    public static function facturasanuladas(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];

        $router->render('admin/reportes/facturas/facturasanuladas', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function facturaselectronicas(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];

        $router->render('admin/reportes/facturas/facturaselectronicas', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    
    public static function facturaselectronicaspendientes(Router $router){
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];
        $router->render('admin/reportes/facturas/electronicaspendientes', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function recibosCaja(Router $router){
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];
        $router->render('admin/reportes/facturas/recibosCaja', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function inventarioxproducto(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];
        $sql = "SELECT sps.stock, p.nombre, p.unidadmedida, p.categoria, p.marca, p.tipoproducto, p.sku, p.stockminimo, p.precio_compra, p.precio_venta, p.fecha_ingreso
                FROM stockproductossucursal sps JOIN productos p ON sps.productoid = p.id
                WHERE p.estado = 1 AND p.visible = 1 AND sps.sucursalid = ".id_sucursal().";";
        $productos = productos::camposJoinObj($sql);

        $sql = "SELECT sis.stock, sp.nombre, sp.unidadmedida, sp.sku, sp.stockminimo, sp.precio_compra, 0 as precio_venta, sp.fecha_ingreso
                FROM stockinsumossucursal sis JOIN subproductos sp ON sis.subproductoid = sp.id
                WHERE sis.sucursalid = ".id_sucursal().";";  
        $subproductos = subproductos::camposJoinObj($sql);

        $router->render('admin/reportes/inventario/inventarioxproducto', ['titulo'=>'Reportes', 'productos'=>$productos, 'subproductos'=>$subproductos, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function movimientosinventarios(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];

        $router->render('admin/reportes/inventario/movimientosinventarios', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function compras(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];

        $router->render('admin/reportes/inventario/compras', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }


    public static function productosComprados(Router $router){
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];
        $router->render('admin/reportes/inventario/productosComprados', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }


    public static function detallecompra(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];
        $id=$_GET['id'];
        if(!is_numeric($id))return;

        $compra = compras::find('id', $id);
        $usuario = usuarios::find('id', $compra->idusuario);
        $proveedor = proveedores::find('id', $compra->idproveedor);
        $caja = caja::find('id', $compra->idorigencaja);
        $detallecompra = detallecompra::idregistros('idcompra', $compra->id);
        $router->render('admin/reportes/inventario/detallecompra', ['titulo'=>'Reportes', 'compra'=>$compra, 'usuario'=>$usuario, 'proveedor'=>$proveedor, 'caja'=>$caja, 'detallecompra'=>$detallecompra, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function printDetalleCompra(Router $router){
        self::detallecompra($router);
    }


    public static function detalleInvoice(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];
        $id=$_GET['id'];  //id de la factura
        if(!is_numeric($id))return;

        $ultimaFacturaElectronica = null;
        $factura = facturas::find('id', $id);
        $facturasElectronicas = facturas_electronicas::whereArray(['id_sucursalidfk'=>id_sucursal(), 'id_facturaid' => $factura->id]);
        $adquiriente = adquirientes::find('id', 1);
        //Obtener la ultima factura electronica por prioridad de estado
        if($facturasElectronicas){
          $max = 0;
          $prioridades = [2 => 3, 1 => 2, 3 => 2, 4 => 1];
          $prioridadActual = 0;
          foreach($facturasElectronicas as $value){
            $p = $prioridades[$value->id_estadoelectronica];
            if($p > $prioridadActual || ($value->id > $max && $p == $prioridadActual)){
              $max = $value->id;
              $ultimaFacturaElectronica = $value;
              $prioridadActual = $p;
            }
          }
          $adquiriente = adquirientes::find('id', $ultimaFacturaElectronica->id_adquiriente);
        }
        $resoluciones = consecutivos::whereArray(['idtipofacturador'=>1, 'id_sucursalid'=>id_sucursal(), 'estado'=>1]);
        $departments = departments::all();

        $router->render('admin/reportes/facturas/detalleinvoice', ['titulo'=>'Reportes', 'idfe'=>$id, 'factura'=>$factura, 'facturasElectronicas'=>$facturasElectronicas, 'ultimaFacturaElectronica'=>$ultimaFacturaElectronica, 'adquiriente'=>$adquiriente, 'resoluciones'=>$resoluciones, 'departments'=>$departments, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }


    public static function utilidadRentabilidad(Router $router){
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];
        /*$ws = new whatsAppService();
        $ws->sendMessage('', '');*/

        $router->render('admin/reportes/utilidadgastoscrecimiento/utilidadRentabilidad', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function utilidadxproducto(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];
        $productos = productos::whereArray(['visible'=>1]);
        $router->render('admin/reportes/utilidadgastoscrecimiento/utilidadxproducto', ['titulo'=>'Reportes', 'productos'=>$productos, 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function gastoseingresos(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];

        $router->render('admin/reportes/utilidadgastoscrecimiento/gastoseingresos', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function clientesnuevos(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];

        $router->render('admin/reportes/otros/clientesnuevos', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }

    public static function clientesrecurrentes(Router $router){
        //session_start();
        isadmin();
        if(!tienePermiso('Habilitar modulo de reportes')&&userPerfil()>=3)return;
        $alertas = [];

        $router->render('admin/reportes/otros/clientesrecurrentes', ['titulo'=>'Reportes', 'sucursales'=>sucursales::all(), 'user'=>$_SESSION, 'alertas'=>$alertas]);
    }





  ////////////////////////////----    API      ----////////////////////////////////

  /**
   * POST /admin/api/consultafechazetadiario.
   *
   * Es llamado por src/ts/caja/fechazetadiario.ts. El controlador conserva la
   * autorización, decodificación del formulario y serialización JSON; fechas,
   * pertenencia y consultas se delegan a CajaReportesService.
   */
  public static function consultafechazetadiario(): void{
    isadmin();
    $cajas = json_decode((string)($_POST['cajas'] ?? '[]'), true);
    $facturadores = json_decode((string)($_POST['facturadores'] ?? '[]'), true);

    $resultado = (new CajaReportesService())->consultarZPorRango(
      (string)($_POST['fechainicio'] ?? ''),
      (string)($_POST['fechafin'] ?? ''),
      is_array($cajas) ? $cajas : [],
      is_array($facturadores) ? $facturadores : [],
      id_sucursal()
    );
    echo json_encode($resultado);
  }

  public static function reporteventamensual(){
    
  }

  //// grafica de ventas menusal año acutal de la vista principal de reportes index.php
  public static function ventasGraficaMensual(){
    //session_start();
    isadmin();
    $data = facturas::ventasGraficaMensual(id_sucursal());
    $label = [];
    $datos = [];
    foreach($data as $value){
        $label[] = $value->periodo;
        $datos[] = $value->total_venta;
    }
    echo json_encode(['label'=>$label, 'datos'=>$datos]);
  }


  //// grafica de ventas diarias mes acutal de la vista principal de reportes index.php
  public static function ventasGraficaDiario(){
    //session_start();
    isadmin();
    $data = facturas::ventasGraficaDiario(id_sucursal());
    $label = [];
    $datos = [];
    foreach($data as $value){
        $label[] = 'dia '.$value->dia;
        $datos[] = $value->total_venta;
    }
    echo json_encode(['label'=>$label, 'datos'=>$datos]);
  }

  ///// grafica "Valor de los productos principales del inventario" vista principal de reportes index.php
  public static function graficaValorInventario(){
    //session_start();
    isadmin();
    $sql = "SELECT SUM(sps.stock*p.precio_compra) AS costoinv, SUM(sps.stock*p.precio_venta) AS valorventa
    FROM stockproductossucursal sps JOIN productos p ON sps.productoid = p.id WHERE sps.sucursalid = ".id_sucursal().";";
    $datos = productos::camposJoinObj($sql);
    echo json_encode(array_shift($datos));
  }


  public static function reportesGenerales(){
    isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $sql = "SELECT v.idproducto, COUNT(v.idproducto) as cantidadrefencia, v.nombreproducto, SUM(v.cantidad) as totalProductosVendidos, v.valorunidad,
                    SUM(v.total) as valorTotal, SUM(COALESCE(v.costo, 0) * v.cantidad) AS costoTotal, SUM(v.total - (COALESCE(v.costo, 0) * v.cantidad)) AS utilidad
              FROM facturas f JOIN ventas v ON f.id = v.idfactura
              WHERE f.fechapago BETWEEN '$fechainicio' AND '$fechafin' AND f.estado = 'Paga' AND f.id_sucursal = $idsucursal
              GROUP BY v.idproducto, v.nombreproducto, v.valorunidad;";
      $productosVendidos = productos::camposJoinObj($sql);

      //calcular medio de pago
      $sql = "SELECT fm.idmediopago, COUNT(fm.idmediopago) as cantidadMP, m.mediopago, SUM(fm.valor) as valor
              FROM facturas f JOIN factmediospago fm ON f.id = fm.id_factura
              JOIN mediospago m ON fm.idmediopago = m.id
              WHERE f.fechapago BETWEEN '$fechainicio' AND '$fechafin' AND f.estado = 'Paga' AND f.tipoventa = 'Contado' AND f.id_sucursal = $idsucursal
              GROUP BY fm.idmediopago, m.mediopago;";
      $mediosPagos = productos::camposJoinObj($sql);

      //creditos/separados
      $sql = "SELECT c.idestadocreditos, ec.nombre as estado, SUM(c.capital+c.valorinterestotal) as carteraTotal, SUM(c.saldopendiente) as carteraXCobrar,
              (SUM(c.capital+c.valorinterestotal)-SUM(c.saldopendiente)) as totalAbonado, COUNT(*) AS total
              FROM creditos c JOIN estadocreditos ec ON c.idestadocreditos = ec.id WHERE c.idtipofinanciacion = 2
              GROUP BY c.idestadocreditos, ec.nombre;";
      $creditoRepo = new creditosRepository();
      $Separados = $creditoRepo->querySQL($sql);

      //calcular descuento
      $sql = "SELECT SUM(f.descuento) AS total_descuentos
              FROM facturas f
              WHERE f.fechapago BETWEEN '$fechainicio' AND '$fechafin' AND f.estado = 'Paga' AND f.id_sucursal = $idsucursal;";
      $totalDescuentos = productos::camposJoinObj($sql);

      //canal de venta
      $sql = "SELECT IFNULL(cv.nombre, 'TOTAL GENERAL') AS canalVenta, COUNT(cv.id) as transacciones, SUM(f.total) AS valor
              FROM facturas f LEFT JOIN canaldeventa cv ON f.idcanaldeventa = cv.id
              WHERE f.fechapago BETWEEN '$fechainicio' AND '$fechafin' AND f.estado = 'Paga' AND f.tipoventa = 'Contado' AND f.id_sucursal = $idsucursal
              GROUP BY cv.nombre WITH ROLLUP";
      $canalVenta = facturas::camposJoinObj($sql);

      //calcular ventas por usuario
      $sql = "SELECT COUNT(f.id) as ventasRealizadas, CONCAT(u.nombre, COALESCE(u.apellido, '')) as empleado, SUM(f.total) as totalVentas, ROUND((SUM(f.porcentgananciauser)/COUNT(f.id)), 2) as porcentaje, SUM(f.valorgananciauser) as valorComision
              FROM facturas f JOIN usuarios u ON f.idvendedor = u.id
              WHERE f.fechapago BETWEEN '$fechainicio' AND '$fechafin' AND f.estado = 'Paga' AND f.tipoventa = 'Contado' AND f.id_sucursal = $idsucursal
              GROUP BY u.id;";
      $ventasXusuario = productos::camposJoinObj($sql);

      //gastos
      $sql = "SELECT IFNULL(cg.nombre, 'TOTAL GENERAL') AS descripcion, IFNULL(g.operacion, '') AS tipogasto, SUM(g.valor) AS valor
              FROM gastos g JOIN categoriagastos cg ON g.idcategoriagastos = cg.id
              WHERE g.fecha BETWEEN '$fechainicio' AND '$fechafin' AND g.id_sucursalfk = $idsucursal
              GROUP BY cg.nombre, g.operacion WITH ROLLUP
              HAVING (cg.nombre IS NOT NULL AND g.operacion IS NOT NULL) OR (cg.nombre IS NULL AND g.operacion IS NULL);";
      $gastos = gastos::camposJoinObj($sql);
      
      //resumen
        //ventas
        $sql = "SELECT COUNT(DISTINCT f.id) as ventas, SUM(v.total) as total_ventas, SUM(COALESCE(v.costo, 0) * v.cantidad) AS total_costo, SUM(v.total - (COALESCE(v.costo, 0) * v.cantidad)) AS ganancia,
                ROUND((SUM(v.total - (COALESCE(v.costo, 0) * v.cantidad))/NULLIF(SUM(v.total), 0))*100, 2) AS margenutilidad
                FROM facturas f JOIN ventas v ON f.id = v.idfactura
                WHERE f.fechapago BETWEEN '$fechainicio' AND '$fechafin' AND f.estado = 'Paga' AND f.tipoventa = 'Contado' AND f.id_sucursal = $idsucursal";
        $resumenVentas = facturas::camposJoinObj($sql);
        //creditos
        $resumenCreditos = $creditoRepo->estadosFinancierosCreditosTotalesFinalizados($fechainicio, $fechafin, $idsucursal);
        
        //total abonos realizados de periodo consultado
        $sql = "SELECT SUM(cu.valorpagado) as totalabonos FROM cuotas cu
                WHERE cu.fechapagado BETWEEN '$fechainicio' AND '$fechafin' AND cu.valorpagado>0 AND cu.id_sucursal_idfk = $idsucursal;";
                //debuguear($sql);
                $cuotasRepo = new cuotasRepository();
                $totalabonos = $cuotasRepo->querySQL($sql)[0]['totalabonos']; 

    }
    echo json_encode(['productosVendidos'=>$productosVendidos, 'mediosPagos'=>$mediosPagos, 'totalDescuentos'=>$totalDescuentos, 'separados'=>$Separados, 'canalVenta'=>$canalVenta, 'ventasXusuario'=>$ventasXusuario, 'gastos'=>$gastos, 'resumenCreditos'=>$resumenCreditos, 'resumenVentas'=>$resumenVentas, 'totalabonos'=>$totalabonos]);
    return;
  }

  
  //transacciones acumuladas por mes durante año elegido
  public static function ventasxtransaccionanual(){
    //session_start();
    isadmin();
    $datex = $_GET['x'];
    $idsucursal = id_sucursal();
    $sql = "SELECT DATE_FORMAT(fechapago, '%Y-%m') AS fecha, COUNT(*) AS num_transacciones,
            ROUND(AVG(total), 2) AS promedio_transaccion, SUM(total) AS total_venta,
            MAX(total) AS transaccion_mas_alta, MIN(total) AS transaccion_mas_baja
            FROM facturas WHERE fechapago >= CONCAT('$datex', '-01-01')
            AND fechapago < DATE_ADD(CONCAT('$datex', '-01-01'), INTERVAL 1 YEAR) AND estado = 'Paga' AND id_sucursal = $idsucursal
            GROUP BY DATE_FORMAT(fechapago, '%Y-%m') ORDER BY fecha;";
    $datos = productos::camposJoinObj($sql);
    echo json_encode($datos);
    return;
  }

  //transacciones acumuladas por dia durante mes y año elegido
  public static function ventasxtransaccionmes(){
    //session_start();
    isadmin();
    $datex = $_GET['x'];
    $idsucursal = id_sucursal();
    $sql = "SELECT DATE(fechapago) AS fecha, COUNT(*) AS num_transacciones,
            ROUND(AVG(total), 2) AS promedio_transaccion, SUM(total) AS total_venta,
            MAX(total) AS transaccion_mas_alta, MIN(total) AS transaccion_mas_baja
            FROM facturas WHERE fechapago >= CONCAT('$datex', '-01')
            AND fechapago < DATE_ADD(CONCAT('$datex', '-01'), INTERVAL 1 MONTH) AND estado = 'Paga' AND id_sucursal = $idsucursal
            GROUP BY DATE(fechapago) ORDER BY fecha;";
    $datos = productos::camposJoinObj($sql);
    echo json_encode($datos);
    return;
  }

  //Ventas acumuladas por cliente en un periodo determinado
  public static function ventasxcliente(){
    //session_start();
    isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $sql = "SELECT c.id, CONCAT(c.nombre,' ',c.apellido) AS nombre, COUNT(f.id) AS cantidad_facturas, SUM(f.total) AS total_ventas
              FROM facturas f JOIN clientes c ON f.idcliente = c.id
              WHERE f.fechapago BETWEEN '$fechainicio' AND '$fechafin' AND f.estado = 'Paga' AND f.id_sucursal = $idsucursal
              GROUP BY c.id, c.nombre;";
      $datos = productos::camposJoinObj($sql);
    }
    echo json_encode($datos);
    return;
  }

  public static function ventaProductsUsers(){
    //session_start();
    isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $sql = "SELECT c.id, CONCAT(c.nombre,' ',c.apellido) AS nombre, COUNT(f.id) AS cantidad_facturas, SUM(f.total) AS total_ventas
              FROM facturas f JOIN clientes c ON f.idcliente = c.id
              WHERE f.fechapago BETWEEN '$fechainicio' AND '$fechafin' AND f.estado = 'Paga' AND f.id_sucursal = $idsucursal
              GROUP BY c.id, c.nombre;";
      $datos = productos::camposJoinObj($sql);
    }
    echo json_encode($datos);
    return;
  }

  //Ventas acumuladas por emisor
  public static function apiReporteEmisores():void{
    isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    $idemisor = $_POST['idemisor'];
    $ingresos = [];
    $ventasXEmisor = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $sql = "SELECT COALESCE(e.nombre, 'Negocio') as emisor, COUNT(t.id) AS numventas, SUM(t.subtotal) as subtotal, SUM(t.base) as base,
              SUM(t.valorimpuestototal) as impuesto, SUM(t.descuento) as descuento, SUM(t.total) AS totalventas, SUM(t.totalMediosPago) AS ingresos
              FROM (
                  SELECT f.id, c.idemisor, f.subtotal, f.base, f.valorimpuestototal, f.descuento, f.total, SUM(fmp.valor) AS totalMediosPago
                  FROM facturas f
                  LEFT JOIN factmediospago fmp ON f.id = fmp.id_factura
                  LEFT JOIN cierrescajas cc ON fmp.cierrecajaid = cc.id
                  LEFT JOIN caja c ON cc.idcaja = c.id
                  WHERE fmp.created_at BETWEEN '$fechainicio' AND '$fechafin' AND f.estado = 'Paga' AND f.id_sucursal = $idsucursal
                  GROUP BY f.id, c.idemisor
              ) t
              LEFT JOIN emisores e ON e.id = t.idemisor
              GROUP BY e.id;";

              /*$sql = "SELECT
    COALESCE(e.nombre, 'Negocio') AS emisor,
    SUM(r.numventas) AS numventas,
    SUM(r.subtotal) AS subtotal,
    SUM(r.base) AS base,
    SUM(r.impuesto) AS impuesto,
    SUM(r.descuento) AS descuento,
    SUM(r.totalventas) AS totalventas,
    SUM(r.ingresos) AS ingresos
FROM (
    /*
     * VENTAS REALIZADAS EN EL PERÍODO
     *
     * Incluye ventas de contado y ventas a crédito,
     * aunque no tengan registros en factmediospago.
     */
    /*SELECT
        f.idemisor,
        COUNT(f.id) AS numventas,
        SUM(f.subtotal) AS subtotal,
        SUM(f.base) AS base,
        SUM(f.valorimpuestototal) AS impuesto,
        SUM(f.descuento) AS descuento,
        SUM(f.total) AS totalventas,
        0 AS ingresos
    FROM facturas f
    WHERE f.fechapago BETWEEN '$fechainicio' AND '$fechafin'
      AND f.estado = 'Paga'
      AND f.id_sucursal = $idsucursal
    GROUP BY f.idemisor

    UNION ALL

    /*
     * DINERO RECIBIDO EN EL PERÍODO
     *
     * Incluye pagos de contado y abonos de facturas
     * creadas en fechas anteriores.
     */
    /*SELECT
        c.idemisor,
        0 AS numventas,
        0 AS subtotal,
        0 AS base,
        0 AS impuesto,
        0 AS descuento,
        0 AS totalventas,
        SUM(fmp.valor) AS ingresos
    FROM factmediospago fmp
    INNER JOIN facturas f
        ON f.id = fmp.id_factura
    LEFT JOIN cierrescajas cc
        ON cc.id = fmp.cierrecajaid
    LEFT JOIN caja c
        ON c.id = cc.idcaja
    WHERE fmp.created_at BETWEEN '$fechainicio' AND '$fechafin'
      AND f.id_sucursal = $idsucursal
    GROUP BY c.idemisor
) r
LEFT JOIN emisores e
    ON e.id = r.idemisor
GROUP BY
    r.idemisor,
    e.nombre;";*/

      $ingresos = productos::camposJoinObj($sql);


      $condicionEmisor = $idemisor===''?'IS NULL':'= '.(int)$idemisor;
      $sql = "SELECT * FROM facturas f WHERE f.idemisor $condicionEmisor AND f.fechapago BETWEEN '$fechainicio' AND '$fechafin' AND f.estado = 'Paga' AND f.id_sucursal = $idsucursal;";
      $ventasXEmisor = productos::camposJoinObj($sql);
    }
    echo json_encode(['ingresos'=>$ingresos, 'ventasXEmisor'=>$ventasXEmisor]);
    return;
  }


  //Facturas procesadas como pagas
  public static function apifacturaspagas():void{
    $facturas = [];
    isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $facturas = facturas::whereArrayBETWEEN('fechapago', $fechainicio, $fechafin, ['estado'=>'Paga', 'id_sucursal'=>$idsucursal]);
    }
    echo json_encode($facturas);
    return;
  }

  public static function apiRemisiones():void{
    $facturas = [];
    isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    $sql = "SELECT *FROM facturas WHERE fechacreacion BETWEEN '$fechainicio' AND '$fechafin' AND remision = 1 AND (estado = 'Remision' OR estado = 'Paga') AND id_sucursal = $idsucursal";
    if($_SERVER['REQUEST_METHOD'] === 'POST' )
      $facturas = facturas::camposJoinObj($sql);
    echo json_encode($facturas);
    return;
  }

  //estado financiero solo de separados
  public static function estadosFinancierosCreditos(){
     isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    $creditosRepo = new creditosRepository;
    $estadosFinancierosCreditos = $creditosRepo->estadosFinancierosCreditos($fechainicio, $fechafin, $idsucursal);
    echo json_encode($estadosFinancierosCreditos);
  }

  public static function apiCuotasCreditos(){
    isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      /*$sql = "SELECT cu.fechapagado, CONCAT(cl.nombre,' ',cl.apellido) as cliente, cr.idtipofinanciacion, cr.num_orden as credito, cr.idestadocreditos, cu.numerocuota, smp.valor as valorpormedio, mp.mediopago
              FROM cuotas cu
              INNER JOIN separadomediopago smp ON cu.id = smp.idcuota
              INNER JOIN mediospago mp ON smp.mediopago_id = mp.id
              INNER JOIN creditos cr ON cu.id_credito = cr.id
              INNER JOIN clientes cl ON cr.cliente_id = cl.id 
              WHERE cu.fechapagado BETWEEN '$fechainicio' AND '$fechafin' AND cu.valorpagado>0 AND cu.id_sucursal_idfk = $idsucursal;";
      */

              $sql = "SELECT 
                        cu.fechapagado,
                        CONCAT(cl.nombre,' ',cl.apellido) AS cliente,
                        cr.idtipofinanciacion,
                        cr.num_orden AS credito,
                        cr.idestadocreditos,
                        cu.numerocuota,
                        smp.valor AS valorpormedio,
                        mp.mediopago
                    FROM cuotas cu
                    INNER JOIN creditos cr ON cu.id_credito = cr.id
                    INNER JOIN clientes cl ON cr.cliente_id = cl.id
                    INNER JOIN separadomediopago smp 
                        ON cu.id = smp.idcuota
                    INNER JOIN mediospago mp 
                        ON smp.mediopago_id = mp.id
                    WHERE cr.idtipofinanciacion = 2
                        AND cu.fechapagado BETWEEN '$fechainicio' AND '$fechafin'
                        AND cu.valorpagado > 0
                        AND cu.id_sucursal_idfk = $idsucursal

                    UNION ALL

                    -- CRÉDITOS (sin duplicar cuotas)
                    SELECT 
                        cu.fechapagado,
                        CONCAT(cl.nombre,' ',cl.apellido) AS cliente,
                        cr.idtipofinanciacion,
                        cr.num_orden AS credito,
                        cr.idestadocreditos,
                        cu.numerocuota,
                        cu.valorpagado AS valorpormedio,
                        pagos.mediopago
                    FROM cuotas cu
                    INNER JOIN creditos cr ON cu.id_credito = cr.id
                    INNER JOIN clientes cl ON cr.cliente_id = cl.id
                    INNER JOIN (
                        SELECT 
                            fmp.id_factura,
                            GROUP_CONCAT(mp.mediopago SEPARATOR ' + ') AS mediopago
                        FROM factmediospago fmp
                        INNER JOIN mediospago mp 
                            ON fmp.idmediopago = mp.id
                        GROUP BY fmp.id_factura
                    ) pagos 
                        ON cr.factura_id = pagos.id_factura
                    WHERE cr.idtipofinanciacion = 1
                        AND cu.fechapagado BETWEEN '$fechainicio' AND '$fechafin'
                        AND cu.valorpagado > 0
                        AND cu.id_sucursal_idfk = $idsucursal;";

      $cuotasRepo = new cuotasRepository();
      $cuotas = $cuotasRepo->querySQL($sql);
    }
    echo json_encode($cuotas);
  }

  //Facturas procesadas que luego fueron anuladas
  public static function apifacturasanuladas(){
    //session_start();
    isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $facturas = facturas::whereArrayBETWEEN('fechapago', $fechainicio, $fechafin, ['estado'=>'Eliminada', 'id_sucursal'=>$idsucursal]);
    }
    echo json_encode($facturas);
  }

  //Facturas electronicas aceptadas
  public static function apifacturaselectronicas(){
    //session_start();
    isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $sql = "SELECT fe.id, fe.id_facturaid as orden, fe.prefijo, fe.numero, fe.cufe, fe.filename, fe.identificacion, fe.nombre, fe.link, fe.id_estadonota,
              fe.nota_credito, fe.prefixnc, fe.num_nota, fe.linknc, fe.filenamenc, f.tipoventa, f.base, f.valorimpuestototal, f.total, fe.created_at
              FROM facturas_electronicas fe
              JOIN facturas f ON fe.id_facturaid = f.id
              JOIN adquirientes a ON fe.id_adquiriente = a.id
              WHERE fe.created_at >= '$fechainicio' AND fe.created_at < DATE_ADD('$fechafin', INTERVAL 1 DAY) AND fe.id_estadoelectronica = 2 AND f.id_sucursal = $idsucursal
              ORDER BY fe.created_at;";
      $datos = productos::camposJoinObj($sql);
    }
    echo json_encode($datos);
  }

  //Facturas electronicas pendientes
  public static function apielectronicaspendientes(){
    //session_start();
    isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $sql = "SELECT fe.id, fe.id_facturaid as orden, fe.prefijo, fe.numero, fe.cufe, fe.identificacion, fe.nombre, f.tipoventa, f.base, f.valorimpuestototal, f.total, fe.created_at
              FROM facturas_electronicas fe 
              JOIN facturas f ON fe.id_facturaid = f.id
              JOIN adquirientes a ON fe.id_adquiriente = a.id
              WHERE fe.created_at BETWEEN '$fechainicio' AND '$fechafin' AND fe.id_estadoelectronica = 1 OR fe.id_estadoelectronica = 3 AND f.id_sucursal = $idsucursal
              ORDER BY fe.created_at;";
      $datos = productos::camposJoinObj($sql);
    }
    echo json_encode($datos);
  }


  public static function apirecibosCaja():void{
    isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $sql = "SELECT mc.id, mc.num_orden, mc.fecha, mc.concepto, mc.observacion, u.nombre AS cajero, mc.valor, e.nombre AS emisor, mc.numero_documento, mc.estado AS estadoMov,
                  cl.identificacion as documento,
                  CONCAT(cl.nombre, ' ', COALESCE(cl.apellido, '')) AS tercero, /* Nombre del tercero */
                  mp.mediopago, /* Medio de pago */
                  fmp.valor AS valormediopago, /* Valor pagado por ese medio */
                  c.nombre AS caja /* Caja */
              FROM movimientos_caja mc
              /* Medios de pago */
              LEFT JOIN factmediospago fmp
                    ON(
                      mc.fk_tipo_documento = 1
                      AND mc.id_documento = fmp.id_factura
                      AND NOT EXISTS (
                          SELECT 1
                          FROM movimientos_caja mc_cuota
                          WHERE mc_cuota.fk_tipo_documento = 2
                            AND mc_cuota.id_documento = fmp.idcuota
                            AND mc_cuota.id_sucursal = mc.id_sucursal
                      )
                    )
                    OR(mc.fk_tipo_documento = 2 AND mc.id_documento = fmp.idcuota)

              LEFT JOIN mediospago mp ON mp.id = fmp.idmediopago
              /* Clientes */
              LEFT JOIN clientes cl ON mc.fk_tipo_tercero = 1 AND cl.id = mc.id_tercero
              /* Usuario */
              LEFT JOIN usuarios u ON u.id = mc.fk_usuario
              /* Caja */
              LEFT JOIN caja c ON c.id = mc.fk_caja
              /* Emisor */
              LEFT JOIN emisores e ON e.id = c.idemisor
              WHERE mc.id_sucursal=$idsucursal AND mc.fecha >= '$fechainicio' AND fecha <= '$fechafin';";
      $datos = productos::camposJoinObj($sql);
    }
    echo json_encode($datos??[]);
    return;
  }

  //Reporte movimiento de inventarios  llamada desde movimientosinventarios.ts
  public static function movimientoInventario(){
    //session_start();
    isadmin();
    $datos = [];
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    $tipo = $_POST['tipo'];
    $iditem = $_POST['iditem'];
    
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      if($tipo == 0){ //productos
        $sql = "SELECT m.id, m.tipo, m.referencia, m.cantidad, m.stockanterior, m.stocknuevo, m.comentario, m.created_at,
                p.id as iditem, p.nombre, p.unidadmedida, CONCAT(u.nombre,' ',u.apellido) as usuario
                FROM movimientos_productos m
                JOIN productos p ON m.idproducto_id = p.id
                JOIN usuarios u ON m.id_usuarioid = u.id
                WHERE m.created_at BETWEEN '$fechainicio' AND '$fechafin' AND idproducto_id = $iditem AND idfksucursal = $idsucursal ORDER BY m.created_at DESC;";
      }else{  //subproductos - insumos
        $sql = "SELECT m.id, m.tipo, m.referencia, m.cantidad, m.stockanterior, m.stocknuevo, m.comentario, m.created_at,
                s.id as iditem, s.nombre, s.unidadmedida, CONCAT(u.nombre,' ',u.apellido) as usuario
                FROM movimientos_insumos m
                JOIN subproductos s ON m.id_subproductoid = s.id
                JOIN usuarios u ON m.idusuario_id = u.id
                WHERE m.created_at BETWEEN '$fechainicio' AND '$fechafin' AND id_subproductoid = $iditem AND fksucursal_id = $idsucursal ORDER BY m.created_at DESC;";
      }
      $datos = ActiveRecord::camposJoinObj($sql);
    }
    echo json_encode($datos);
  }

  //Reporte de compras
  public static function reportecompras(){
    //session_start();
    isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $sql = "SELECT c.id, c.formapago, c.nfactura, c.impuesto, c.cantidaditems, c.observacion, c.estado, c.subtotal, c.valortotal, c.fechacompra,
              cc.estado AS estadocierrecaja, CONCAT(u.nombre,' ',u.apellido) as nombreusuario, p.nombre as nombreproveedor, cj.nombre as nombrecaja
              FROM compras c
              LEFT JOIN gastos g ON c.id = g.id_compra
              LEFT JOIN cierrescajas cc ON g.idg_cierrecaja = cc.id
              JOIN usuarios u ON c.idusuario = u.id 
              JOIN proveedores p ON c.idproveedor = p.id 
              JOIN caja cj ON c.idorigencaja = cj.id
              WHERE c.id_sucursal_id = $idsucursal AND c.fechacompra BETWEEN '$fechainicio' AND '$fechafin' ORDER BY c.fechacompra DESC;";
      $datos = productos::camposJoinObj($sql);
    }
    echo json_encode($datos);
  }

  public static function eliminarcompra(){
    //session_start();
    isadmin();
    $idsucursal = id_sucursal();
    $alertas = [];
    $compra = compras::uniquewhereArray(['id'=>$_POST['id'], 'id_sucursal_id'=>$idsucursal]);
    $detallecompra = detallecompra::idregistros('idcompra', $compra->id);
    $gasto = gastos::uniquewhereArray(['id_compra'=>$compra->id, 'id_sucursalfk'=>$idsucursal]);
    $rsps = true;
    $rsis = true;
    //////////  SEPARAR LOS ITEMS EN PRODUCTOS Y SUBPRODUCTOS  ////////////
    $resultArray = array_reduce($detallecompra, function($acumulador, $objeto){
      if($objeto->tipo == 0){
        $objeto->id = $objeto->idpx;
        $acumulador['productos'][] = $objeto; // puede ser producto compuesto o simple
      }
      else{
        $objeto->id = $objeto->idsx;
        $acumulador['subproductos'][] = $objeto;
      }
      return $acumulador;
    }, ['productos'=>[], 'subproductos'=>[]]);

    
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $cierrecaja = cierrescajas::find('id', $gasto->idg_cierrecaja);
      if($cierrecaja->estado == 0){ //si cierre de caja esta abierto
        //eliminar compra 
        $rc = $compra->eliminar_registro(); // tambien se elimina el gasto por cascada de la tabla
        if($rc){
          if($gasto->id_banco!=null){ //ajustar gasto banco del cierre de caja
            $cierrecaja->gastosbanco -= $gasto->valor;
          }else{ //ajustar gasto caja efectivo del cierre de caja
            $cierrecaja->gastoscaja -= $gasto->valor;
          }
          $rcc = $cierrecaja->actualizar();
          if($rcc){
            //descontar del inventario
            if(!empty($resultArray['productos']))$rsps = stockproductossucursal::reduceinv1condicion($resultArray['productos'], 'stock', 'productoid', 'sucursalid = '.$idsucursal);
            if(!empty($resultArray['subproductos']))$rsis = stockinsumossucursal::reduceinv1condicion($resultArray['subproductos'], 'stock', 'subproductoid', 'sucursalid = '.$idsucursal);
            if($rsps&&$rsis){
              $alertas['exito'][] = "Compra eliminada correctamente";
            }else{
              $alertas['error'][] = "No se pudo eliminar la compra del cierre de caja";
              $compra->crear_guardar();
              $gasto->crear_guardar();
              //dejar el inventario original
            }
          }else{
            $alertas['error'][] = "No se pudo eliminar la compra del cierre de caja";
            $compra->crear_guardar();
            $gasto->crear_guardar();
          }
        }else{
          $alertas['error'][] = "No se pudo eliminar la compra";
        }
      }else{
        $alertas['error'][] = "Caja ya se encuentra cerrada";
      }
    }
    echo json_encode($alertas);
  }


  //Reporte de productos comprados
  public static function listaProductosComprados(){
    isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];

    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $sql = "SELECT
                c.id AS idcompra,
                c.nfactura,
                c.fechacompra,
                c.estado,
                c.formapago,
                c.subtotal,
                c.valortotal,

                -- Usuario
                CONCAT_WS(' ', u.nombre, u.apellido) as usuario,

                -- Proveedor
                pr.nit AS nitproveedor,
                pr.nombre AS proveedor,

                -- Detalle
                dc.id AS iddetalle,
                dc.idpx,
                dc.idsx,
                dc.tipo,
                dc.unidad,
                dc.cantidad,
                dc.factor,
                dc.impuesto,
                /*dc.valorunidad AS costounitario,*/ dc.valorcompra / NULLIF(dc.cantidad, 0) AS costounitario,
                dc.subtotal AS subtotal,
                dc.valorcompra AS costototal,

                -- Tipo de item
                CASE
                    WHEN dc.idpx IS NOT NULL THEN 'PRODUCTO'
                    WHEN dc.idsx IS NOT NULL THEN 'INSUMO'
                    ELSE 'SIN TIPO'
                END AS tipo_item,

                -- Nombre del producto o insumo
                COALESCE(p.nombre, sp.nombre, dc.nombreitem) AS nombre_item,
                
                -- Stock actual de la sucursal
                COALESCE(sps.stock, sis.stock, 0) AS stock_actual

            FROM compras c

            INNER JOIN detallecompra dc
                ON dc.idcompra = c.id

            LEFT JOIN usuarios u
                ON u.id = c.idusuario

            LEFT JOIN proveedores pr
                ON pr.id = c.idproveedor

            LEFT JOIN productos p
                ON p.id = dc.idpx

            LEFT JOIN subproductos sp
                ON sp.id = dc.idsx

            -- Stock de productos
            LEFT JOIN stockproductossucursal sps
                ON sps.productoid = dc.idpx
                AND sps.sucursalid = c.id_sucursal_id

            -- Stock de insumos
            LEFT JOIN stockinsumossucursal sis
                ON sis.subproductoid = dc.idsx
                AND sis.sucursalid = c.id_sucursal_id

            WHERE c.id_sucursal_id = $idsucursal AND c.fechacompra >= '$fechainicio' AND c.fechacompra <= '$fechafin'
            ORDER BY c.fechacompra DESC, c.id DESC, dc.id ASC;";
      $datos = productos::camposJoinObj($sql);
    }
    echo json_encode($datos);
    return;
  }

  
  //Reporte de gastos e ingresos llamado desde gastoseingresos.ts
  public static function apigastoseingresos(){
    //session_start();
    isadmin();
    $idsucursal = id_sucursal();
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      //calculo de los gastos
      $sql = "SELECT g.id AS Id, g.fecha, CONCAT(u.nombre,' ',u.apellido) AS nombre, g.id_banco, b.nombre AS nombrebanco, g.idg_caja, c.nombre AS nombrecaja,
	            g.idg_cierrecaja, cj.estado, g.id_compra, g.operacion, g.idg_usuario, g.valor, g.descripcion, g.imgcomprobante, cg.id , cg.nombre AS categoriagasto
              FROM gastos g JOIN categoriagastos cg ON g.idcategoriagastos = cg.id
              JOIN usuarios u ON g.idg_usuario = u.id
              JOIN cierrescajas cj ON g.idg_cierrecaja = cj.id
              LEFT JOIN bancos b ON g.id_banco = b.id
              JOIN caja c ON g.idg_caja = c.id
	            WHERE g.id_sucursalfk = $idsucursal AND g.fecha BETWEEN '$fechainicio' AND '$fechafin' ORDER BY g.fecha DESC;";
      $getgastos = gastos::camposJoinObj($sql);

      //calculo de los ingresos a caja
      $sql = "SELECT i.id, i.operacion, i.valor, i.fecha, c.nombre AS nombrecaja,
	            cj.estado, CONCAT(u.nombre,' ',u.apellido) AS nombreusuario
              FROM ingresoscajas i 
              JOIN usuarios u ON i.idusuario = u.id
              JOIN caja c ON i.id_caja = c.id
              JOIN cierrescajas cj ON i.id_cierrecaja = cj.id
              WHERE  i.idsucursal_idfk = $idsucursal AND i.fecha BETWEEN '$fechainicio' AND '$fechafin' ORDER BY i.fecha DESC;";
      $getingresos = ingresoscajas::camposJoinObj($sql);
    }

    echo json_encode(['gastos'=>$getgastos, 'ingresos'=>$getingresos]);
  }


  //Reporte de gastos e ingresos llamado desde gastoseingresos.ts
  public static function eliminargasto(){
    //session_start();
    isadmin();
    $idsucursal = id_sucursal();
    $alertas = [];
    $gasto = gastos::uniquewhereArray(['id'=>$_POST['id'], 'id_sucursalfk'=>$idsucursal]);
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $cierrecaja = cierrescajas::find('id', $gasto->idg_cierrecaja);
      if($cierrecaja->estado == 0){ //si cierre de caja esta abierto
        //eliminar gasto 
        $re = $gasto->eliminar_registro();
        //eliminar img comprobante del gasto
        $existe_archivo = file_exists($_SERVER['DOCUMENT_ROOT']."/build/img/".$gasto->imgcomprobante);
        if($existe_archivo && !empty($gasto->imgcomprobante))unlink($_SERVER['DOCUMENT_ROOT']."/build/img/".$gasto->imgcomprobante);
        if($re){
          if($gasto->id_banco!=null){ //ajustar gasto banco del cierre de caja
            $cierrecaja->gastosbanco -= $gasto->valor;
          }else{ //ajustar gasto caja efectivo del cierre de caja
            $cierrecaja->gastoscaja -= $gasto->valor;
          }
          $rc = $cierrecaja->actualizar();
          if($rc){
            $alertas['exito'][] = "Gasto eliminado correctamente";
          }else{
            $alertas['error'][] = "No se pudo eliminar el gasto del cierre de caja";
            $gasto->crear_guardar();
          }
        }else{
          $alertas['error'][] = "No se pudo eliminar el gasto";
        }
      }else{
        $alertas['error'][] = "Caja ya se encuentra cerrada";
      }
    }
    echo json_encode($alertas);
  }


  //Reporte de gastos e ingresos efectivos "base" llamado desde gastoseingresos.ts
  public static function eliminaringresocaja(){
    //session_start();
    isadmin();
    $idsucursal = id_sucursal();
    $alertas = [];
    $ingresocaja = ingresoscajas::uniquewhereArray(['id'=>$_POST['id'], 'idsucursal_idfk'=>$idsucursal]);
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      $cierrecaja = cierrescajas::find('id', $ingresocaja->id_cierrecaja);
      if($cierrecaja->estado == 0){ //si cierre de caja esta abierto
        //eliminar ingreso efectivo "base" caja 
        $ri = $ingresocaja->eliminar_registro();
        if($ri){
          //ajustar ingreso efectivo "base" a caja efectivo del cierre de caja
          $cierrecaja->basecaja -= $ingresocaja->valor;
          $rc = $cierrecaja->actualizar();
          if($rc){
            $alertas['exito'][] = "Ingreso eliminado correctamente";
          }else{
            $alertas['error'][] = "No se pudo eliminar el ingreso del cierre de caja";
            $ingresocaja->crear_guardar();
          }
        }else{
          $alertas['error'][] = "No se pudo eliminar el ingreso";
        }
      }else{
        $alertas['error'][] = "Caja ya se encuentra cerrada";
      }
    }
    echo json_encode($alertas);
  }

}
