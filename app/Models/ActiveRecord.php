<?php
namespace App\Models;
class ActiveRecord {

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // Alertas y Mensajes
    protected static $alertas = [];
    
    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) { //este metodo se llama por primera vez en app.php
        self::$db = $database; //self hace referencia a atributos y metodos estatic de la clase padre o clase actual, es decir a la propiedad $db de la clase padre activerecord
    }

    public static function getDB(){
        return self::$db;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }

    // Validación
    public static function getAlertas() {
        return static::$alertas;
    }

    public function validar() {
        static::$alertas = [];
        return static::$alertas;
    }

    public function eliminar_registro(){
        $sqldelete = "DELETE FROM ".static::$tabla." WHERE id = ".self::$db->escape_string($this->id)." LIMIT 1";
        //debuguear($this);  $this tiene el objeto actual instanciado
        $resultado = self::$db->query($sqldelete);
        if(!$resultado)throw new \Exception("Error al eliminar registro". self::$db->error);
        return $resultado;
    } 


    public static function eliminar_idregistros($id, $array = []){  //'id', [11, 8]
        $sqldelete = "DELETE FROM ".static::$tabla." WHERE ${id} IN (";
        foreach($array as $key => $value){
            if(array_key_last($array) == $key){ 
                $sqldelete.= self::$db->escape_string($value).");";
            }else{
                $sqldelete.= self::$db->escape_string($value).", ";
            }
        }
        //DELETE FROM empserv WHERE id IN (11, 12);
        $resultado = self::$db->query($sqldelete);
        return $resultado;  
    }


    public static function eliminar_wherearray($array = []){  //$array['id_producto'=>1, 'id_subproducto'=>4]
        $sqldelete = "DELETE FROM ".static::$tabla." WHERE ";
        foreach($array as $key => $value){
            if(array_key_last($array) == $key){ 
                $sqldelete.= "$key = ".self::$db->escape_string($value).";";
            }else{
                $sqldelete.= "$key = ".self::$db->escape_string($value)." AND ";
            }
        }
        //DELETE FROM productos_sub WHERE id_producto = 2 AND id_subproducto = 6;
        $resultado = self::$db->query($sqldelete);
        return $resultado;
    }


    public function crear_guardar(){
        $atributos = $this->sanitizar_datos();  //$atributos obtiene arreglo ['titulo'=>valor1, 'precio'=>valor2] 
        $string1 = join(', ', array_keys($atributos));  //array_keys  = ['llave1', 'llave2', 'llave3', ....]
        $string2 = join("', '", array_values($atributos)); // array_values = [valor1', 'valor2', 'valor3', '....]
        $sql = "INSERT INTO ".static::$tabla."(";
        $sql .= $string1;
        $sql .= ") VALUES('";
        $sql .= $string2;
        $sql .= "');";
        $sql = str_replace("''", 'NULL', $sql);
        $resultado = self::$db->query($sql);
        if(!$resultado)throw new \Exception("Error al crear registro". self::$db->error);
        return [$resultado, self::$db->insert_id];  //insert_id retorna el ultimo registro insertado en la bd
           //  [true/false, id=1,2,3...00] = [0,1] 
    }


    public function crear_varios_reg($arrays = []){ //guardar varios registros a la vez
                                  // $arrays = [['colum11', 'colum12',...],['colum21', 'colum22',..], ...] arrays contiene los valores de los registros a guardar
        $string2 = '';

        foreach($arrays as $key => $value){
            ////// este ciclo se hace por lo que no se esta instanciando el objeto, con esto tomo el objeto y le cargo valores
            foreach(static::$columnasDB as $columdb){
                if($columdb == 'id')continue;
                $this->$columdb = $value[$columdb];
            }
            /////ya se lleno la primera vez el objeto////
            $atributos = $this->sanitizar_datos();
            if(array_key_last($arrays) == $key){
                $string2 .= "('".join("', '", array_values($atributos))."');";
            }else{
                $string2 .= "('".join("', '", array_values($atributos))."'), ";
            }
        }
        $string2 = str_replace("'NULL'", 'NULL', $string2);
        $string1 = join(', ', array_keys($atributos));
        $sql = "INSERT INTO ".static::$tabla."(".$string1.") VALUES".$string2;
        $resultado = self::$db->query($sql);
        //INSERT INTO empserv(idempleado, idservicio) VALUES('3', '3'), ('3', '1');
        return [$resultado, self::$db->insert_id];
    }

    public function crear_varios_reg_arrayobj($arrays = []):array{ //guardar varios registros a la vez
                                  // $arrays = [{'colum11':a, 'colum12':b,...}, {'colum21':q, 'colum22':e,..}, ...] arrays de objetos que contiene las columnas y su valor, registros a guardar
        $string2 = '';

        foreach($arrays as $key => $value){
            ////// este ciclo se hace por lo que no se esta instanciando el objeto, con esto tomo el objeto y le cargo valores
            foreach(static::$columnasDB as $columdb){
                if($columdb == 'id' || !property_exists($value, $columdb))continue;
                $this->$columdb = $value->$columdb;
            }/////ya se lleno la primera vez el objeto no instanciado !primer registro¡////

            $atributos = $this->sanitizar_datos();
            if(array_key_last($arrays) == $key){
                $string2 .= "('".join("', '", array_values($atributos))."');";
            }else{
                $string2 .= "('".join("', '", array_values($atributos))."'), ";
            }
        }

        $string2 = str_replace("'NULL'", 'NULL', $string2);
        $string1 = join(', ', array_keys($atributos));
        $sql = "INSERT INTO ".static::$tabla."(".$string1.") VALUES".$string2;
        $resultado = self::$db->query($sql); 
        //INSERT INTO empserv(idempleado, idservicio) VALUES('3', '3'), ('3', '1');
        if(!$resultado)throw new \Exception("Error al crear registros");
        return [$resultado, self::$db->insert_id];
    }


    public function actualizar(){
        $atributos = $this->sanitizar_datos(); 
        $valores = [];
        foreach($atributos as $key => $value ){
            //if($key === "fechacreacion")$value = date('y-m-d'); //
            if($value == ''){
                $valores[] = "{$key}=NULL";
            }else{
                $valores[] = "{$key}='{$value}'"; //$valores = [llave1='valor1', llave2='valor2',...]
            }
        }
        $query = "UPDATE ".static::$tabla." SET ";
        $query .= join(", ", $valores); // = "llave1='valor1', llave2=>valor2,...."
        $query .= " WHERE id = '".self::$db->escape_string($this->id)."'";
        $query .= " LIMIT 1;";
        $resultado = self::$db->query($query);
        if(!$resultado)throw new \Exception("Error al actualizar el registro");
        return $resultado;
    }


    //actualizar multiples registros, pero simpre y cuando las filas tengan los mismos valores en cada columna (valores, ids)
    public static function actualizar_ids($atributos=[], $ids=[]){
        $valores = [];
        foreach($atributos as $key => $value){
            $atributos[$key] = self::$db->escape_string($value);  
        }
        foreach($atributos as $key => $value ){
            $valores[] = "{$key}='{$value}'"; //$valores = [llave1='valor1', llave2='valor2',...]
        }
        $query = "UPDATE ".static::$tabla." SET ";
        $query .= join(", ", $valores); // = "llave1='valor1', llave2=>valor2,...."
        $query .= " WHERE id IN (";
        foreach($ids as $index => $element){
            if(array_key_last($ids) == $index){  //toma la ultima llave y la compara con la llave actual
                $query.= self::$db->escape_string($element).");";
            }else{
                $query.= self::$db->escape_string($element).", ";
            }
        } 
        //UPDATE estud_grupo SET idnivel='2', idgrupo='8' WHERE id IN (1, 3, 5);
        $resultado = self::$db->query($query);
        return $resultado;
    }


    //actualizar multiples filas donde cada fila tiene valores diferentes en cada columna
    public static function updatemultiple($array=[], $ids=[]){  //array = ['colum1'=>[fila1, fila2..], 'colum2'=>[fila1, fila2..]...]
        $valores = [];
        $query = "UPDATE ".static::$tabla." SET ";
        foreach($array as $keycolum => $colum){
            $query .= $keycolum." = CASE ";
            $j = 0;
            foreach($colum as $key => $value){
                $atributos[$keycolum][$key] = self::$db->escape_string($value);
                $query .= "WHEN id = {$ids[$j]} THEN '{$value}' ";
                $j++;
            } 
            if(array_key_last($array) === $keycolum){
                $query .= "ELSE {$keycolum} END";
            }else{
                $query .= "ELSE {$keycolum} END, ";
            } 
        }
        $query .= " WHERE id IN (";
        $query .= join(", ", array_values($ids));
        $query .= ");";
        //UPDATE nombretabla SET idnivel = CASE WHEN id = 1 THEN '2' WHEN id = 3 THEN '4' WHEN id = 5 THEN '6' ELSE idnivel END, idgrupo = CASE WHEN id = 1 THEN '8' WHEN id = 3 THEN '10' WHEN id = 5 THEN '12' ELSE idgrupo END WHERE id IN (1, 3, 5);
        $resultado = self::$db->query($query);
        return $resultado;
    }


    //actualizar multiples filas donde cada fila tiene valores diferentes en cada columna
    public static function updatemultiregobj($array=[], $colums=[]){  //array = [{id:1, tipo:0}, {}...]
        $query = "UPDATE ".static::$tabla." SET ";          //$coums = ['stock', 'precio_compra',..]
        $in = "";
        foreach($colums as $idx => $col){
            $query .= $col." = CASE ";
            foreach($array as $index => $value){
                if(array_key_first($colums) === $idx){
                    $query .= "WHEN id = $value->id THEN '{$value->$col}' ";
                    if(array_key_last($array) === $index){
                        $in .= "$value->id";
                    }else{
                        $in .= "$value->id, ";
                    }
                }else{
                    $query .= "WHEN id = $value->id THEN '{$value->$col}' ";
                }
            }
            if(array_key_last($colums) === $idx){
                $query .= "ELSE $col END WHERE id IN ($in);";
            }else{
                $query .= "ELSE $col END, ";
            }
        }//UPDATE nombretabla SET idnivel = CASE WHEN id = 1 THEN '2' WHEN id = 3 THEN '4' WHEN id = 5 THEN '6' ELSE idnivel END, idgrupo = CASE WHEN id = 1 THEN '8' WHEN id = 3 THEN '10' WHEN id = 5 THEN '12' ELSE idgrupo END WHERE id IN (1, 3, 5);
        $resultado = self::$db->query($query);
        return $resultado;
    }


    //////  Actualiza multiples registros pero un solo campo, $array es arreglo de objetos y $col es el campo a actualizar
    public static function updatereduceinv($array, $col){  //$array = [{idproducto: "1", idcategoria: "3", nombre: "xxx", cantidad: "4"}, {}]
        $query = "UPDATE ".static::$tabla." SET $col = CASE ";
        $in = "";
        foreach($array as $index => $value){
            $query .= "WHEN id = $value->id THEN $col - $value->cantidad ";
            if(array_key_last($array) === $index){
                $in .= "$value->id";
                $query .= "ELSE {$col} END WHERE id IN ($in);";
            }else{
                $in .= "$value->id, ";
            }
        }
        //UPDATE productos SET stock = CASE WHEN id = 2 THEN stock - 2 WHEN id = 3 THEN stock - 1 WHEN id = 4 THEN stock - 3 ELSE stock END WHERE id IN (2, 3, 4);
        $resultado = self::$db->query($query);
        return $resultado;
    }

    public static function reduceinv1condicion($array, $col, $id, $stringCondicion){  //$array = [{idproducto: "1", idcategoria: "3", nombre: "xxx", cantidad: "4"}, {}], $col = 'stock'
        $query = "UPDATE ".static::$tabla." SET $col = CASE ";
        $in = "";
        foreach($array as $index => $value){
            $query .= "WHEN $id = $value->id THEN $col - $value->cantidad ";
            if(array_key_last($array) === $index){
                $in .= "$value->id";  //$value->id = idsubproducto
                $query .= "ELSE {$col} END WHERE $id IN ($in) AND $stringCondicion;";
            }else{
                $in .= "$value->id, ";
            }
        }
        //UPDATE productos SET stock = CASE WHEN id = 2 THEN stock - 2 WHEN id = 3 THEN stock - 1 ELSE stock END WHERE id IN (2, 3) AND sucursalid = 1;
        $resultado = self::$db->query($query);
        if(!$resultado)throw new \Exception("Error al descontar stock");
        return $resultado;
    }


    public static function reducirMultiplesColumnas(array $array, array $columnas, string $id, string $condicion = '1=1'){
        if(empty($array))return false;
        $query = "UPDATE " . static::$tabla . " SET ";
        $sets = [];
        $ids = [];
        foreach($columnas as $col){
            $case = "$col = CASE ";
            foreach($array as $item){
                $itemId = (int)$item->id;
                $cantidad = (float)$item->$col;
                $case .= "WHEN $id = $itemId THEN $col - $cantidad ";
                $ids[] = $itemId;
            }
            $case .= " ELSE $col END ";
            $sets[] = $case;
        }
        $ids = array_unique($ids);
        $query .= implode(", ", $sets);
        $query .= " WHERE $id IN (" . implode(',', $ids) . ") AND $condicion";
        return self::$db->query($query);
    }


    public static function updateaddinv($array, $col){  //$array = [{idproducto: "1", idcategoria: "3", nombre: "xxx", cantidad: "4"}, {}]
        $query = "UPDATE ".static::$tabla." SET $col = CASE ";
        $in = "";
        foreach($array as $index => $value){
            $query .= "WHEN id = $value->idproducto THEN $col + $value->cantidad ";
            if(array_key_last($array) === $index){
                $in .= "$value->idproducto";
                $query .= "ELSE {$col} END WHERE id IN ($in);";
            }else{
                $in .= "$value->idproducto, ";
            }
        }
        //UPDATE productos SET stock = CASE WHEN id = 2 THEN stock + 2 WHEN id = 3 THEN stock + 1 WHEN id = 4 THEN stock + 3 ELSE stock END WHERE id IN (2, 3, 4);
        $resultado = self::$db->query($query);
        return $resultado;
    }


    public static function addinv($array, $col){  //$array = [{idproducto: "1", idcategoria: "3", nombre: "xxx", cantidad: "4"}, {}]
        $query = "UPDATE ".static::$tabla." SET $col = CASE ";
        $in = "";
        foreach($array as $index => $value){
            $query .= "WHEN id = $value->id THEN $col + $value->cantidad ";
            if(array_key_last($array) === $index){
                $in .= "$value->id";
                $query .= "ELSE {$col} END WHERE id IN ($in);";
            }else{
                $in .= "$value->id, ";
            }
        }
        //UPDATE productos SET stock = CASE WHEN id = 2 THEN stock + 2 WHEN id = 3 THEN stock + 1 WHEN id = 4 THEN stock + 3 ELSE stock END WHERE id IN (2, 3, 4);
        $resultado = self::$db->query($query);
        return $resultado;
    }


    public static function addinv1condicion($array, $col, $id, $stringCondicion){  //$array = [{idproducto: "1", idcategoria: "3", nombre: "xxx", cantidad: "4"}, {}], $col = 'stock'
        $query = "UPDATE ".static::$tabla." SET $col = CASE ";
        $in = "";
        foreach($array as $index => $value){
            $query .= "WHEN $id = $value->id THEN $col + $value->cantidad ";
            if(array_key_last($array) === $index){
                $in .= "$value->id";  //id = idproducto
                $query .= "ELSE {$col} END WHERE $id IN ($in) AND $stringCondicion;";
            }else{
                $in .= "$value->id, ";
            }
        }
        //UPDATE productos SET stock = CASE WHEN id = 2 THEN stock + 2 WHEN id = 3 THEN stock + 1 WHEN id = 4 THEN stock + 3 ELSE stock END WHERE id IN (2, 3, 4) AND sucursalid = 1;
        $resultado = self::$db->query($query);
        if(!$resultado)throw new \Exception("Error al descontar stock");
        return $resultado;
    }


    public static function aumentarMultiplesColumnas(array $array, array $columnas, string $id, string $condicion = '1=1'){
        if(empty($array))return false;
        $query = "UPDATE " . static::$tabla . " SET ";
        $sets = [];
        $ids = [];
        foreach($columnas as $col){
            $case = "$col = CASE ";
            foreach($array as $item){
                $itemId = (int)$item->id;
                $cantidad = (float)$item->$col;
                $case .= "WHEN $id = $itemId THEN $col + $cantidad ";
                $ids[] = $itemId;
            }
            $case .= " ELSE $col END ";
            $sets[] = $case;
        }
        $ids = array_unique($ids);
        $query .= implode(", ", $sets);
        $query .= " WHERE $id IN (" . implode(',', $ids) . ") AND $condicion";
        return self::$db->query($query);
    }


    public static function camposaddinv($array, $colums){  //$array = [{id: "1", idcategoria: "3", nombre: "xxx", cantidad: "4"}, {}]
        $query = "UPDATE ".static::$tabla." SET ";          //$coums = ['stock', 'precio_compra',..]
        $in = "";
        foreach($colums as $idx => $col){
            $query .= $col." = CASE ";
            foreach($array as $index => $value){
                if(array_key_first($colums) === $idx){
                    $query .= "WHEN id = $value->id THEN $col + $value->cantidad ";
                    if(array_key_last($array) === $index){
                        $in .= "$value->id";
                    }else{
                        $in .= "$value->id, ";
                    }
                }else{
                    $query .= "WHEN id = $value->id THEN {$value->$col} ";
                }
            }
            if(array_key_last($colums) === $idx){
                $query .= "ELSE $col END WHERE id IN ($in);";
            }else{
                $query .= "ELSE $col END, ";
            }
        }//UPDATE nombretabla SET stock = CASE WHEN id = 1 THEN stock + 2 WHEN id = 3 THEN stock + 1 ELSE stock END, valor = CASE WHEN id = 1 THEN '800' WHEN id = 3 THEN '100' ELSE valor END WHERE id IN (1, 3, 5);
        $resultado = self::$db->query($query);
        return $resultado;
    }


    public static function actualizarLibre($consulta):bool{
        return self::$db->query($consulta);
    }

    
    public function actualizar_referencia($copyreferencia){   //actualiza el id, referencia o llave primaria
        $atributos = $this->sanitizar_datos(); 
        $valores = [];
        foreach($atributos as $key => $value ){
            if($key === "fechacreacion")$value = date('y-m-d'); //
            $valores[] = "{$key}='{$value}'"; //$valores = [llave1='valor1', llave2='valor2',...]
        }
        $query = "UPDATE ".static::$tabla." SET ";
        $query .= join(", ", $valores);
        $query .= " WHERE Referencia = '".self::$db->escape_string($copyreferencia)."'";
        $query .= " LIMIT 1;";
        $resultado = self::$db->query($query);
        return $resultado;
    }


    public function sanitizar_datos(){
        $atributos = $this->atributos();  //devuelve arreglo asociativo ['titulo'=>valor_titulo, 'precio'=>valor_precio]
        $sanitizado = [];
        foreach($atributos as $key => $value){
            if($value == null){
                $sanitizado[$key] = $value;
            }else{
                $sanitizado[$key] = self::$db->escape_string($value);  //arreglo sanitizado ['titulo'=>valor_titulo, 'precio'=>valor_precio] pero protegido pra inyecciones sql
            }
        }
        return $sanitizado;  //se retorna arreglo[] ya sanitizado
    }


    public function atributos(){
        $atributos = [];
        foreach(static::$columnasDB as $columna){  //el arreglo $columnas_db toma valores de la clase hija
            if($columna == 'id') continue;  
            $atributos[$columna] = $this->$columna;    //columna va iterando y va tomando valores como 'titulo', 'precio'
        }      
        return $atributos;  //retorna arreglo asociativo ['titulo'=>$this->titulo, 'precio'=>$this->precio]
    }


    public static function all(){ //se trae todos los registros de la tabla de la bd
        $sql = "SELECT *FROM ".static::$tabla;  //accede al atributo protegido, static es un modificador de acceso, static busca el atributo tabla en la clase el cual se hereda
        $resultado = self::consultar_Sql($sql); 
        return $resultado;  //$resultado es un arreglo de objetos
    }


    public static function get($cantidad){  
        $sql = "SELECT *FROM ".static::$tabla." LIMIT ".$cantidad;
        $resultado = self::consultar_sql($sql);  //self hace referencia a la clase padre, self solo funciona dentro de metodos
        return $resultado;  //$resultado es arreglo de objetos
    }


    public static function filtro_nombre($columna, $nombre, $orden){   //metodo que trae los registros con el nombre especificado y los ordena descendentemente
        $sql = "SELECT *FROM ".static::$tabla." WHERE {$columna} LIKE "."'%{$nombre}%'"." ORDER BY {$orden} DESC;";
        $resultado = self::consultar_sql($sql);
        return $resultado;
    }


    public static function idregistros($colum, $id){ ////metodo que busca todos los registro que pertenecen a un id
        $sql = "SELECT *FROM ".static::$tabla." WHERE $colum = '${id}';";
        $resultado = self::consultar_Sql($sql);
        foreach($resultado as $instancia)
            self::cargarRelaciones($instancia);
        return $resultado;
    }


    public static function idregistrosForUpdate(string $colum, int $id, bool $cargaRelacional = true){ ////metodo que busca todos los registro que pertenecen a un id
        $sql = "SELECT *FROM ".static::$tabla." WHERE $colum = '${id}' FOR UPDATE;";
        $resultado = self::consultar_Sql($sql);
        if($cargaRelacional)
            foreach($resultado as $instancia)
                self::cargarRelaciones($instancia);
        return $resultado;
    }


    //busca un solo registro por su id, se puede usar para validar registros de login con email
    public static function find($colum, $id){
        $sql = "SELECT *FROM ".static::$tabla." WHERE $colum = '${id}' LIMIT 1;";
        $resultado = self::consultar_Sql($sql);
        $instancia = array_shift($resultado); //array_shift retorna el primer elemento del arreglo
        return self::cargarRelaciones($instancia);
    }

    //busca un solo registro por su id, con bloque FOR UPDATE
    public static function findForUpdate($colum, $id){
        $sql = "SELECT *FROM ".static::$tabla." WHERE $colum = '${id}' LIMIT 1 FOR UPDATE";
        $resultado = self::consultar_Sql($sql);
        return array_shift($resultado); //array_shift retorna el primer elemento del arreglo
    }

    protected static function cargarRelaciones(object|null $instancia){
        if(!$instancia)return $instancia;
        if(!property_exists($instancia, 'with'))return $instancia;
        foreach($instancia->with as $metodo)
            if (method_exists($instancia, $metodo))
                $instancia->$metodo = $instancia->$metodo();

        return $instancia;
    }

    /**
     * Bloquea varias filas por su llave primaria dentro de una transaccion.
     * Los IDs se ordenan para que operaciones concurrentes adquieran los
     * bloqueos en el mismo orden y reduzcan la posibilidad de interbloqueos.
     */
    public static function findManyForUpdate(array $ids):array{
        $ids = array_values(array_unique(array_filter( array_map('intval', $ids), static fn(int $id):bool => $id > 0 )));
        sort($ids, SORT_NUMERIC);
        if(empty($ids))return [];

        $sql = "SELECT * FROM ".static::$tabla." WHERE id IN (".implode(', ', $ids).")"." ORDER BY id ASC FOR UPDATE";
        return self::consultar_Sql($sql);
    }

    //metodo de consulta libre utilizando lenguaje sql de inner_join
    public static function inner_join($consulta){
        $resultado = self::consultar_sql($consulta);
        return $resultado;
    }

    //metodo de consulta libre utilizando lenguaje sql de inner_join
    public static function camposJoinObj($consulta):array|NULL{
        $resultado = self::$db->query($consulta);
        $array = [];
        while($row = $resultado->fetch_object()){  
            $array[] = $row;
        }
        $resultado->free();  //[{'id':'1', 'costo':'23'}, {}]  cada obj es una fila de la tabla
        return $array;
    }

    //metodo de consulta libre utilizando lenguaje sql donde cada posicion del arreglo asociativo es el valor de un campo de la tabla
    public static function sqlLibreIndexKey($consulta, $colum):array|NULL{
        $resultado = self::$db->query($consulta);
        $array = [];
        while($row = $resultado->fetch_object()){  
            $array[$row->$colum] = $row;
        }
        $resultado->free();  //['columna'=>{'id':'1', 'costo':'23'}, 'columna'=>{}]  cada obj es una fila de la tabla
        return $array;
    }


    public static function tableAJoin2TablesWhereId(string $tablaB, string $columTableB, string $idTableB)
    {//me une 2 tablas con el id de la primera tabla, y se trae el registro de la primera tabla, cuando se cumple la condicion del id de la segunda tabla.
        $sql = "SELECT ".static::$tabla.".* FROM ".static::$tabla." JOIN $tablaB ON ".static::$tabla.".id = $tablaB.$columTableB WHERE $tablaB.id = $idTableB;";  //
        $resultado = self::consultar_sql($sql);
        return array_shift($resultado);
    }

    //traer el ultimo indice de una tabla
    public static function ultimoindice(){
        $sql = "SELECT MAX(id) FROM ".static::$tabla." LIMIT 1;";
        $resultado = self::$db->query($sql);
        $r = $resultado->fetch_assoc();
        $resultado->free();
        return array_shift($r);
    }
    
    //metodo solo llamado desde clase hija que tenga la propiedad public email, y se debe instanciar clase hija para llamar este metodo
    public function validar_registro(){
        $sql = "SELECT * FROM ".static::$tabla." WHERE email = '".$this->email."' LIMIT 1;";  // propiedad email se defina en clase hija, y desde la clase hija se hereda este metodo de validar_registro(), por lo tanto este metodo solo debe ser llamado desde objeto con clase hija, clase hija q tenga la propiedad public email
        $resultado = self::$db->query($sql);
        return $resultado->num_rows;  //si hay registro num_rows = 1, si no hay registro num_rows = 0
    }

    public function validar_regDinamic(string $col): int
    {
        $sql = "SELECT * FROM ".static::$tabla." WHERE $col = '".$this->$col."' LIMIT 1;";  // propiedad email se defina en clase hija, y desde la clase hija se hereda este metodo de validar_registro(), por lo tanto este metodo solo debe ser llamado desde objeto con clase hija, clase hija q tenga la propiedad public email
        $resultado = self::$db->query($sql);
        return $resultado->num_rows;  //si hay registro num_rows = 1, si no hay registro num_rows = 0
    }


    //numero total de registros de una tabla
    public static function numregistros(){
        $sql = "SELECT COUNT(*) FROM ".static::$tabla.";";
        $resultado = self::$db->query($sql);
        $total = $resultado->fetch_assoc();
        return array_shift($total);
    }

    
    //numero total de registros de una tabla q cumplan una condicion
    public static function numreg_where($colum, $id){
        $sql = "SELECT COUNT(*) FROM ".static::$tabla." WHERE $colum = '${id}';";
        $resultado = self::$db->query($sql);
        $total = $resultado->fetch_assoc();
        return array_shift($total);
    }

    //numero total de registros de una tabla q cumplan multiples condiciones de una sola columna
    public static function numreg_multiwhere($colum, $datos=[]){
        $sql = "SELECT COUNT(*) FROM ".static::$tabla." WHERE $colum IN("; // WHERE $colum = '${id}';";
        foreach($datos as $key => $value){
            if(array_key_last($datos) == $key){ 
                $sql.= $value.");";
            }else{
                $sql.= $value.", ";
            }
        } //SELECT COUNT(*) FROM citas WHERE id_empserv IN(24, 25, 37);
        $resultado = self::$db->query($sql);
        $total = $resultado->fetch_assoc();
        return array_shift($total);
    }

    //numero total de registros de una tabla q cumplan multiples condiciones de varias columnas
    public static function numreg_multicolum($colums=[]){
        $sql = "SELECT COUNT(*) FROM ".static::$tabla." WHERE ";
        foreach($colums as $key => $value){
            if(array_key_last($colums) == $key){ 
                $sql.= "$key = '$value';";
            }else{
                $sql.= "$key = '$value' AND ";
            }
        } //SELECT COUNT(*) FROM citas WHERE id_usuario = 2 AND estado = 'Pendiente';
        $resultado = self::$db->query($sql);
        $total = $resultado->fetch_assoc();
        return array_shift($total);
    }

    //suma numerica de una columna segun id
    public static function sumcolum($colum, $id, $colsum){
        $sql = "SELECT SUM($colsum) FROM ".static::$tabla." WHERE $colum = '${id}';";
        $resultado = self::$db->query($sql);
        $total = $resultado->fetch_assoc();
        return array_shift($total);
    }


    //paginar registros toma numero de registros por pagina y el offset
    public static function paginar($Nregistros_porpagina, $offset){
        $sql = "SELECT *FROM ".static::$tabla." ORDER BY id DESC LIMIT ".$Nregistros_porpagina." OFFSET ".$offset;
        $resultado = self::consultar_sql($sql);  
        return $resultado;  
    }

    //paginar registros q cumplan ciertas condiciones, toma numero de registros por pagina y el offset
    //tambien me trae todos los registros que cumplan una condicion, asi la condicion se repita
    public static function paginarwhere($Nregistros_porpagina, $offset, $colum, $array = []){
        $sql = "SELECT *FROM ".static::$tabla." WHERE $colum IN(";
        foreach($array as $key => $value){
            if(array_key_last($array) == $key){
                $sql.= "'{$value}');";
            }else{
                $sql.= "'{$value}', ";
            }
        }//SELECT *FROM productos_sub WHERE id_subproducto IN(3, 1);
        $resultado = [];
        if(!empty($array))$resultado = self::consultar_sql($sql);
        return $resultado;  
    }

    public static function IN_Where($colum, $array = [], $filter=[]){
        $sql = "SELECT *FROM ".static::$tabla." WHERE $colum IN(";
        foreach($array as $key => $value){
            if(array_key_last($array) == $key){
                if(count($filter)>0){
                    $sql.= "'{$value}') AND $filter[0] = $filter[1];";
                }else{
                    $sql.= "'{$value}');";  
                }
            }else{
                $sql.= "'{$value}', ";
            }
        }//SELECT *FROM productos_sub WHERE id_subproducto IN(3, 1) AND idsucursal = 1;
        $resultado = [];
        if(!empty($array))$resultado = self::consultar_sql($sql);
        return $resultado;  
    }

    //// busqueda con where con multiples opciones
    public static function whereArray($array = [], $orden = "ASC"){ //$array = ['confirmado'=>1, 'admin'=>0]
        $sql = "SELECT *FROM ".static::$tabla." WHERE ";
        foreach($array as $key => $value){
            if(array_key_last($array) == $key){
                $sql.= " ${key} = '${value}'";
            }else{
                $sql.= " ${key} = '${value}' AND ";
            }
        }
        $sql .= " ORDER BY id $orden;";
        $resultado = self::consultar_Sql($sql);
        return $resultado;
    }


    //// busqueda con where con multiples opciones
    public static function whereArrayBETWEEN($coldate, $fechaini, $fechafin, $array = []){ //$array = ['confirmado'=>1, 'admin'=>0]
        $sql = "SELECT *FROM ".static::$tabla." WHERE $coldate BETWEEN '$fechaini' AND '$fechafin' AND";
        foreach($array as $key => $value){
            if(array_key_last($array) == $key){
                $sql.= " ${key} = '${value}'";
            }else{
                $sql.= " ${key} = '${value}' AND ";
            }
        }
        $resultado = self::consultar_Sql($sql);
        return $resultado;
    }


    //// busqueda con where haciendo un join de 2 tablas con multiples opciones, 
    public static function unJoinWhereArray($Obj, $t1id, $t2id, $array = []):array{ //$array = ['confirmado'=>1, 'admin'=>0]
        $sql = "SELECT ".static::$tabla.".*, ".$Obj::$tabla.".*, ".static::$tabla.".id AS ID"." FROM ".static::$tabla." JOIN ".$Obj::$tabla." ON ".static::$tabla.".".$t1id." = ".$Obj::$tabla.".".$t2id." WHERE";
        foreach($array as $key => $value){
            if(array_key_last($array) == $key){
                $sql.= " ${key} = '${value}';";
            }else{
                $sql.= " ${key} = '${value}' AND ";
            }
        }
        $resultado = self::$db->query($sql);        //SELECT productos_sub.*, subproductos.*, productos_sub.id AS ID FROM productos_sub JOIN subproductos
        $arreglo = [];                              //ON productos_sub.id_subproducto = subproductos.id
        while($row = $resultado->fetch_assoc())     // WHERE productos_sub.id_producto = '2';
        $arreglo[] = $row;
        $resultado->free();
        return $arreglo;
    }


    public static function unJoinWhereArrayObj($Obj, $t1id, $t2id, $array = []):array{ //$array = ['confirmado'=>1, 'admin'=>0]
        $sql = "SELECT ".static::$tabla.".*, ".$Obj::$tabla.".*, ".static::$tabla.".id AS ID"." FROM ".static::$tabla." JOIN ".$Obj::$tabla." ON ".static::$tabla.".".$t1id." = ".$Obj::$tabla.".".$t2id." WHERE";
        foreach($array as $key => $value){
            if(array_key_last($array) == $key){
                $sql.= " ${key} = '${value}';";
            }else{
                $sql.= " ${key} = '${value}' AND ";
            }
        }
        $resultado = self::$db->query($sql);        //SELECT productos_sub.*, subproductos.*, productos_sub.id AS ID FROM productos_sub JOIN subproductos
        $arreglo = [];                              //ON productos_sub.id_subproducto = subproductos.id
        while($row = $resultado->fetch_object())     // WHERE productos_sub.id_producto = '2';
        $arreglo[] = $row;
        $resultado->free();
        return $arreglo;
    }


    //// se trae todos los registros haciendo un join de 2 tablas
    public static function unJoinAll($Obj, $t1id, $t2id):array{
        $sql = "SELECT ".static::$tabla.".*, ".$Obj::$tabla.".*, ".static::$tabla.".id AS ID"." FROM ".static::$tabla." JOIN ".$Obj::$tabla." ON ".static::$tabla.".".$t1id." = ".$Obj::$tabla.".".$t2id.";";
        //SELECT *FROM conversionunidades JOIN subproductos ON conversionunidades.idsubproducto = subproductos.id;
        $resultado = self::$db->query($sql);
        $arreglo = [];
        while($row = $resultado->fetch_assoc())
        $arreglo[] = $row;
        $resultado->free();
        return $arreglo;
    }


    //// busqueda con where con multiples opciones trae un solo registro el primero que coincida
    public static function uniquewhereArray($array = []){ //$array = ['confirmado'=>1, 'admin'=>0]
        $sql = "SELECT *FROM ".static::$tabla." WHERE ";
        foreach($array as $key => $value){
            if(array_key_last($array) == $key){
                $sql.= " ${key} = '${value}'";
            }else{
                $sql.= " ${key} = '${value}' AND ";
            }
        }
        $resultado = self::consultar_Sql($sql);
        return array_shift($resultado);
    }


    public static function uniquewhereArrayForUpdate($array = []){ //$array = ['confirmado'=>1, 'admin'=>0]
        $sql = "SELECT *FROM ".static::$tabla." WHERE ";
        foreach($array as $key => $value){
            if(array_key_last($array) == $key){
                $sql.= " ${key} = '${value}'";
            }else{
                $sql.= " ${key} = '${value}' AND ";
            }
        }
        $sql = $sql." FOR UPDATE";
        $resultado = self::consultar_Sql($sql);
        return array_shift($resultado);
    }


    public static function ordenar($columna, $orden){
        $sql = "SELECT *FROM ".static::$tabla." ORDER BY ${columna} ${orden};";
        $resultado = self::consultar_sql($sql);
        return $resultado;
    }


    public static function ordenarlimite($columna, $orden, $limite){
        $sql = "SELECT *FROM ".static::$tabla." ORDER BY ${columna} ${orden} "."LIMIT ${limite}";
        $resultado = self::consultar_sql($sql);
        return array_shift($resultado);
    }
    

    public static function uncampo($colum, $id, $campo){ /// se trae un solo valor o campo segun el id que se le pase
        $sql = "SELECT ".static::$tabla.".".$campo." FROM ".static::$tabla." WHERE $colum = '${id}';";
        $resultado = self::$db->query($sql);
        $valorcampo = $resultado->fetch_assoc();
        return array_shift($valorcampo);
    }


    public static function multicampos($colum, $id, $campo){ /// se trae varios valores o campos segun el id que se le pase
        $sql = "SELECT ".static::$tabla.".".$campo." FROM ".static::$tabla." WHERE $colum = '${id}';";
        $resultado = self::$db->query($sql);
        $array = [];
        while($row = $resultado->fetch_assoc()){  
            $array[] = $row[$campo];
        }
        $resultado->free();
        return $array;
    }

    public static function consultar_sql($sql){
        $resultado = self::$db->query($sql);  // consulta la bd se trae toda la tabla 
        $array = []; 
        while($row = $resultado->fetch_assoc()){  //$row obtiene un registro a la vez desde el primero de la tabla de la bd
            $array[] = self::crear_objeto($row);   //Row = ['id'=>1, 'titulo'=>'xxxx'...] $row es un arreglo asociativo
        }
        $resultado->free();
        return $array;  //$array es un arreglo de objetos
    }


    public static function crear_objeto($row){
        $objeto = new static;  //crea un objeto con los atributos vacios y el tipo depende si se llamada del padre o del hijo
        foreach($row as $key => $value){  //Row es un arreglo asociativo donde las llaves son los atributos del objeto o clase
            if(property_exists($objeto, $key)){   // Esta función comprueba si la propiedad dada por $key existe en la clase o objeto especificada
                $objeto->$key = $value;
            }
        } 
        return $objeto;  //retorna el nuevo objeto creado dinamicamneto con los atributos llenos donde los atriburos son los campos de la bd..
    }


    //compara el objeto creado con los valores del $_POST, y remplaza solo los valores del $post
    public function compara_objetobd_post($args){
        foreach($args as $key => $value){      //$key = a la llave del arreglo asociativo y $value es el valor de esa llave del arreglo asociativo
            if(property_exists($this, $key) && !is_null($value)){   //property_exist revisa de un objeto que un atributo exista
               $this->$key = $value;                 //$this hace referencia al objeto actual, objeto va tomando los valores del $_POST                
            }
        }  
    }
  
}
