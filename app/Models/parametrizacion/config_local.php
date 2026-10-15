<?php

namespace App\Models\parametrizacion;

class config_local extends \App\Models\ActiveRecord{
    protected static $tabla = 'config_local';
    protected static $columnasDB = ['id', 'fk_sucursalid', 'clave', 'valor'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->fk_sucursalid = id_sucursal();
        $this->clave = $args['clave']??'';
        $this->valor = $args['valor']??'';
        $this->created_at = $args['created_at']??'';
    }


    public static function getParamGlobal(): array{
        $idsucursal = id_sucursal();
        $sql = "SELECT c.clave, COALESCE(cs.valor, c.valor_default) AS valor_final, c.valor_default, cs.valor AS valor_local
                FROM config_global c LEFT JOIN config_local cs ON c.clave = cs.clave AND cs.fk_sucursalid = $idsucursal;";
        $array = self::sqlLibreIndexKey($sql, 'clave');
        return $array;
    }

    public static function getParamCaja(): array{
        $idsucursal = id_sucursal();
        $sql = "SELECT c.clave, COALESCE(cs.valor, c.valor_default) AS valor_final, c.valor_default, cs.valor AS valor_local
                                FROM config_global c LEFT JOIN config_local cs ON c.clave = cs.clave AND cs.fk_sucursalid = $idsucursal WHERE c.modulo = 'Caja' OR c.modulo = 'Sistema' OR c.modulo = 'Impresion' OR c.modulo = 'WhatsApp';";
        $array = self::sqlLibreIndexKey($sql, 'clave');
        return $array;
    }

    public static function getPasseords(): array{
        $idsucursal = id_sucursal();
        $sql = "SELECT c.clave, COALESCE(cs.valor, c.valor_default) AS valor_final, c.valor_default, cs.valor AS valor_local
                FROM config_global c LEFT JOIN config_local cs ON c.clave = cs.clave AND cs.fk_sucursalid = $idsucursal WHERE c.modulo = 'Claves';";
        $array = self::camposJoinObj($sql);
        return $array;
    }

    ////////// /// Metodos usados por restaurant /////////////

    //Resolver el impuesto de la sucursa
    public static function impuestoVentaSucursal(int $sucursalId, ?int $impuestoProducto): string{
        $db = self::getDB();
        $sql = "SELECT g.clave, COALESCE(l.valor, g.valor_default) AS valor
                FROM config_global g
                LEFT JOIN config_local l ON l.clave = g.clave
                AND l.fk_sucursalid = ? WHERE g.modulo = 'Impuesto' AND g.clave IN ('discriminar_impuesto_por_producto', 'porcentaje_de_impuesto')";
        $stmt = $db->prepare($sql);
        try{
            $stmt->bind_param('i', $sucursalId);
            $stmt->execute();
            $config = [];
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()){
                $config[$row['clave']] = $row['valor'];
            }
        }finally{
            $stmt->close();
        }

        $modo = $config['discriminar_impuesto_por_producto'] ?? null;
        if($modo !== '0' && $modo !== '1')
            throw new \RuntimeException('No está configurado el modo de impuesto.');
        $valor = $modo === '0' ? ($config['porcentaje_de_impuesto'] ?? null) : $impuestoProducto;

        if($valor === null || $valor === '')return 'excluido';
        $porcentaje = filter_var($valor, FILTER_VALIDATE_INT);
        if($porcentaje === false || $porcentaje < 0 || $porcentaje > 100)
            throw new \RuntimeException('El porcentaje de impuesto no es válido.');
        
        return (string) $porcentaje;
    }
    
}