<?php

namespace App\Models\inventario;

class subproductos extends \App\Models\ActiveRecord {
    protected static $tabla = 'subproductos';
    protected static $columnasDB = ['id', 'id_unidadmedida', 'insumoprocesado', 'unidadmedida', 'nombre', 'sku', 'proveedor', 'descripcion', 'medidas', 'color', 'uso', 'stock', 'stockminimo', 'precio_compra', 'fecha_ingreso'];
    
    //public $id;
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->id_unidadmedida = $args['id_unidadmedida']??1;
        $this->insumoprocesado = $args['insumoprocesado']??0; //0 = si es comprado, 1 = si es producido en planta
        $this->unidadmedida = $args['unidadmedida'] ?? '';
        $this->nombre = $args['nombre'] ?? '';
        $this->sku = $args['sku'] ?? '';
        $this->proveedor = $args['proveedor'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->medidas = $args['medidas'] ?? '';
        $this->color = $args['color '] ?? '';
        $this->uso = $args['uso'] ?? '';
        $this->stock = $args['stock'] ?? 0;
        $this->stockminimo = $args['stockminimo']??1;
        $this->precio_compra = $args['precio_compra'] ?? 0;
        $this->fecha_ingreso = $args['fecha_ingreso'] ?? date('Y-m-d H:i:s');
    }

    // Validación para productos nuevos
    public function validar_nuevo_subproducto():array {
        if(!$this->unidadmedida)self::$alertas['error'][] = 'El Nombre de la unidad de medida es Obligatorio';
        
        if(!$this->nombre)self::$alertas['error'][] = 'El Nombre del producto es Obligatorio';
        
        if(strlen($this->nombre)>95)self::$alertas['error'][] = 'El Nombre del producto no debe superar los 52 caracteres';
        
        if($this->sku)
          if(strlen($this->sku)>15)self::$alertas['error'][] = 'El codigo del producto no debe ser mayor a 15 digitos';
        
        if($this->descripcion)
          if(strlen($this->descripcion)>249)self::$alertas['error'][] = 'La descripcion no debe supear los 249 caracteres';
        
        return self::$alertas;
    }

    
    public function equivalencias(int $idsubproducto, int $idunidadbase):array|null {
      $arrayunidadesmedida = ['1'=>'unidad', '2'=>'gramos', '3'=>'libras', '4'=>'kilogramos', '5'=>'milimetros', '6'=>'centimetros', '7'=>'metros', '8'=>'mililitros', '9'=>'litros'];
      $equivalencias = [];
      // Factores de conversión
      $factores = [
          '1' => [ //1=unidad
              '1' => 1
          ],
          '2' => [ //2=gramos
              '2' => 1,
              '3' => 453.592,   //453.592 gramos equivale a 1 libra
              '4' => 1000  //1000 gramos equivale a 1 kilogramo
          ],
          '3' => [  //3=libras
              '3' => 1,
              '2' => 0.002,
              '4' => 2.204
          ],
          '4' => [ //4=kilogramos
              '4' => 1,
              '2' => 0.001,
              '3' => 0.453
          ],
          '5' => [ //5=milimetros
              '5' => 1,
              '6' => 10,      //10 milimetros = 1 centimetro 
              '7' => 1000    //1000 milimetros = 1 metro
          ],
          '6' => [  //6=centimetros
              '6' => 1,
              '5' => 0.1,    // 0.1 centimetro = 1 milimetro
              '7' => 100     // 100cm = 1mt
          ],
          '7' => [ //7=metros
              '7' => 1,
              '5' => 0.001,  //0.001 mt  =  1 mm
              '6' => 0.01    //0.01 mt  =  1 cm
          ],
          '8' => [ //8=mililitros
              '8' => 1,   
              '9' => 1000   //1000 ml = 1lt
          ],
          '9' => [  //9=litros
              '9' => 1,
              '8' => 0.001   //0.001lt = 1ml 
          ]
      ];

      // Verificar si la unidad base está en los factores de conversión
      if(array_key_exists($idunidadbase, $factores)){
        // Generar conversiones para cada unidad destino
        foreach ($factores[$idunidadbase] as $unidaddestino => $factorconversion) {
          $equivalencias[] = (object) [
              'idproducto' => 'NULL',
              'idsubproducto' => $idsubproducto,
              'idunidadmedidabase' => $idunidadbase,
              'idunidadmedidadestino' => $unidaddestino,
              'nombreunidadbase' => $arrayunidadesmedida[$idunidadbase],
              'nombreunidaddestino' => $arrayunidadesmedida[$unidaddestino],
              'factorconversion' => $factorconversion
          ];
        }
      }else{
        $nuevaunidadmedida = unidadesmedida::find('id', $idunidadbase);
        if($nuevaunidadmedida){
            $equivalencias[] = (object) [
                'idproducto' => 'NULL',
                'idsubproducto' => $idsubproducto,
                'idunidadmedidabase' => $idunidadbase,
                'idunidadmedidadestino' => $idunidadbase,
                'nombreunidadbase' => $nuevaunidadmedida->nombre,
                'nombreunidaddestino' => $nuevaunidadmedida->nombre,
                'factorconversion' => 1
            ];
        }else{
            return null;
        }
      }
      return $equivalencias;
    }
    

    public static function indicadoresAllSubProducts():array|NULL{
        $query="SELECT *, SUM(stock*precio_compra) OVER () AS valorinv, 
        COUNT(id) OVER () AS cantidadreferencias, 
        SUM(stock) OVER () AS cantidadproductos,
        SUM(CASE WHEN stock < 10 THEN 1 ELSE 0 END) OVER () AS bajostock,
        SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) OVER () AS productosagotados
        FROM ".self::$tabla.";";
        $array = self::camposJoinObj($query);
        return $array;
      }


      public static function formatExportExcel():array{
        $idsucursal = id_sucursal();
        $sql = "SELECT s.id as ID, s.id_unidadmedida AS ID_UNIDAD, s.insumoprocesado AS INSUMO_PROCESADO, s.nombre AS NOMBRE_INSUMO,
                s.sku AS SKU, sis.stock AS STOCK, sis.stockminimo AS STOCK_MINIMO, s.precio_compra AS PRECIO_COMPRA 
                FROM subproductos s JOIN stockinsumossucursal sis ON s.id = sis.subproductoid WHERE sis.sucursalid = $idsucursal;";
        $array = self::camposJoinObj($sql);
        return $array;
    }
}