<?php

namespace App\Repositories\creditos;

use App\Models\creditos\productosseparados;
use App\Repositories\operationRepository;

class productsSeparadosRepository extends operationRepository{

    //private $db;
    protected string $table = 'productosseparados';
    protected string $entityClass = productosseparados::class;
    protected array $allowedColumns = [];

    public function __construct(/*$conexion*/)
    {
        //$this->db = $conexion;
        $model = new $this->entityClass();
        $this->allowedColumns = array_keys($model->toArray());
    }

    
    /*public function obtenerPorCredito(int $id):array{

        $sql = "SELECT *FROM productosseparados WHERE idcredito = $id;";
        $resultado = $this->db->query($sql);
        while($row = $resultado->fetch_assoc()){ 
            $array[] = new productosseparados($row);   
        }
        $resultado->free();
        return $array;

    }*/

    public function obtenerPorCredito(int $id):array{
        $sql = "SELECT *FROM productosseparados WHERE idcredito = $id;";
        $rows = $this->fetchAll($sql);
        $array = array_map(fn($v) => new productosseparados($v), $rows);
        return $array;
    }


    public function detalleProductosCredito(int $id):array{
        $sql = "SELECT ps.id, ps.idcredito, ps.nombreproducto, ps.tipoproducto, ps.tipoproduccion, ps.costo, ps.valorunidad, ps.cantidad, ps.subtotal, ps.base, ps.valorimp, ps.descuento, ps.total, c.capital, c.abonoinicial, c.saldopendiente, c.cantidadcuotas, c.interes,
                       c.interestotal, c.valorinterestotal, c.montototal, c.descuento as descuentocredito, p.id as idproducto, p.nombre, p.sku, p.preciopersonalizado, p.stockminimo, p.rendimientoestandar, p.impuesto, und.id as idunidadmedida, und.nombre as unidadmedida
                FROM productosseparados ps
                LEFT JOIN creditos c ON ps.idcredito = c.id
                LEFT JOIN productos p ON ps.idproducto = p.id
                LEFT JOIN unidadesmedida und ON p.idunidadmedida = und.id
                WHERE p.visible = 1 AND ps.idcredito = $id;";
        return $this->querySQL($sql);
    }

    /*public function crear_guardar():array{

        return [];
    }*/

    /*public function allowedColumns():void{
        $model = new $this->entityClass();
        $this->allowedColumns = array_keys($model->toArray());
    }*/
    
}