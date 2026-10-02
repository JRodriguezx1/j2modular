<?php 

namespace App\services;


use App\Models\configuraciones\usuarios;
use App\Models\contable\movimientos_caja;
use App\Models\sucursales;
use App\Repositories\contable\movimientos_cajaRepository;
use stdClass;


class contableService{

    private $repoMovimientocaja;
    
    public function __construct()
    {
        $this->repoMovimientocaja = new movimientos_cajaRepository();
    }


    public function createMovimiento(array $data):array{
        $data['num_orden'] = $this->repoMovimientocaja->getNumOrden(id_sucursal());
        return $this->repoMovimientocaja->insert(new movimientos_caja($data));
    }


    public function anularMovimiento(int $typeDocument, int $idDocument, string $observacion = ''):bool{
        $movCaja = $this->repoMovimientocaja->uniqueWhere(['fk_tipo_documento'=>$typeDocument, 'id_documento'=>$idDocument]);
        if(!$movCaja || (int)$movCaja->estado !== 1)return false;

        $movCaja->fecha_anulacion = date('Y-m-d H:i:s');
        $observacion = trim($observacion);
        if($observacion !== ''){
            $actual = trim((string)($movCaja->observacion ?? ''));
            $movCaja->observacion = $actual === '' ? $observacion : "{$actual} - {$observacion}";
        }
        $movCaja->estado = 0;
        return $this->repoMovimientocaja->update($movCaja);
    }
    

    
}
