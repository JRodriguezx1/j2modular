<?php

namespace App\Models;

class gastos extends \App\Models\ActiveRecord {
    protected static $tabla = 'gastos';
    protected static $columnasDB = ['id', 'id_sucursalfk', 'idg_usuario', 'idpagos_comisiones', 'id_compra', 'id_banco', 'idg_caja', 'idg_cierrecaja', 'idcategoriagastos', 'tipo_origen', 'operacion', 'valor', 'descripcion', 'imgcomprobante', 'fecha'];
    
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->id_sucursalfk = id_sucursal();
        $this->idg_usuario = $args['idg_usuario'] ?? 1;
        $this->idpagos_comisiones = $args['idpagos_comisiones']??NULL;
        $this->id_compra = $args['id_compra']??NULL;
        $this->id_banco = $args['id_banco'] ??'';
        $this->idg_caja = $args['idg_caja'] ??'';
        $this->idg_cierrecaja = $args['idg_cierrecaja'] ?? '';
        $this->idcategoriagastos = $args['idcategoriagastos'] ?? 1;
        $this->tipo_origen = $args['tipo_origen'] ??0;  //0 = origen es caja,   1 = origen es banco
        $this->operacion = $args['operacion'] ?? 'gasto';
        $this->valor = $args['valor'] ?? 0;
        $this->descripcion = $args['descripcion'] ?? '';
        $this->imgcomprobante = $args['imgcomprobante']??'';
        $this->fecha = $args['fecha'] ?? date('Y-m-d H:i:s');
    }


    public function validar():array
    {
        if(!$this->idg_usuario)self::$alertas['error'][] = "Error con usuario de sistema";
        //if(!$this->idg_caja)self::$alertas['error'][] = "Caja no seleccionada";
        if(!$this->idg_cierrecaja)self::$alertas['error'][] = "Error con la apertura del dia de caja";
        if(!$this->operacion)self::$alertas['error'][] = "Error con la operacion gasto de la caja";
        if(!$this->valor)self::$alertas['error'][] = "Valor gasto no ingresado";
        if(strlen($this->descripcion)>244)self::$alertas['error'][] = "Has excecido el limite de caracteres de la descripcion";
        return self::$alertas;
    }

    public function validarimgcomprobante($FILE) {
      if($FILE['imgcomprobante']['name'] && $FILE['imgcomprobante']['size']>31000000) {
          self::$alertas['error'][] = 'La foto no puede pasar los 500KB';
      }
      if($FILE['imgcomprobante']['name'] && $FILE['imgcomprobante']['type']!="image/jpeg" && $FILE['imgcomprobante']['type']!="image/png") {
          self::$alertas['error'][] = 'Seleccione una imagen en formato jpeg o png';
      }
      return self::$alertas;
    }
}