<?php

namespace App\Models\caja;

class declaracionesdineros extends \App\Models\ActiveRecord{
    protected static $tabla = 'declaracionesdineros';
    protected static $columnasDB = ['id', 'id_mediopago', 'idcierrecajaid', 'nombremediopago', 'valordeclarado', 'valorsistema'];
    
    public function __construct($args = []){
        $this->id = $args['id']??null;
        $this->id_mediopago = $args['id_mediopago']??'';
        $this->idcierrecajaid = $args['idcierrecajaid']??'';
        $this->nombremediopago = $args['nombremediopago']??'';
        $this->valordeclarado = $args['valordeclarado']??'0';
        $this->valorsistema = $args['valor']??0;
    }


    public function validar():array
    {
        if(!$this->id_mediopago)self::$alertas['error'][] = "Error al declarar el medio de pago";
        if(!$this->idcierrecajaid)self::$alertas['error'][] = "Error con el id cierre de caja";
        if(!$this->nombremediopago)self::$alertas['error'][] = "Error con el nombre del medio de pago declarado";
        if(!is_numeric($this->valordeclarado))self::$alertas['error'][] = "Valor declardo no especificado";
        return self::$alertas;
    }
}

?>