<?php 

namespace App\Rules;

use App\Models\felectronicas\facturas_electronicas;

//**REGLA PARA VALIDAR SI SE PUEDE GENERAR O REGENERAR UNA FACTURA ELECTRONICA DE VENTA
class FacturaElectronicaRules {

    public static function validarSiPuedeGenerarFE($factura, $idConsecutivo, $numero): array {

        $res = null;
        $feExistentes = facturas_electronicas::whereArray(['id_facturaid' => $factura->id]);
        foreach ($feExistentes as $f) {
            if($f->id_estadoelectronica == 2 && $f->nota_credito == 0)
                return [ 'success' => false, 'message' => "La factura electronica está aceptada sin nota crédito.", 'reuse'   => false ];
            if(in_array($f->id_estadoelectronica, [1, 3]))
                return [ 'success' => false, 'message' => "La factura electronica esta pendiente o con error.", 'reuse' => false];
            if($f->id_estadoelectronica == 4 && $f->consecutivo_id == $idConsecutivo && $f->numero == $numero)
                $res = [ 'success' => true, 'reuse' => true,'fe' => $f ];
        }
        if($res!=null)return $res;


        $feExistentes = facturas_electronicas::whereArray([
            'id_sucursalidfk' => id_sucursal(),
            'consecutivo_id' => $idConsecutivo,
            'numero' => $numero
        ]);

        foreach ($feExistentes as $fe) {
            if ($fe->id_estadoelectronica == 2 && $fe->nota_credito == 0) {
                return [
                    'success' => false,
                    'message' => "Existe una factura electronica aceptada sin nota crédito.",
                    'reuse'   => false
                ];
            }

            if (in_array($fe->id_estadoelectronica, [1,3])) {
                return [
                    'success' => false,
                    'message' => "Existe una factura electronica pendiente o con error.",
                    'reuse'   => false
                ];
            }

            if ($fe->id_estadoelectronica == 4) {
                return [
                    'success' => true,
                    'reuse'   => true,
                    'fe'      => $fe
                ];
            }
        }

        return ['success' => true, 'reuse' => false];
    }
}
