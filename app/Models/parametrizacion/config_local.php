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
}