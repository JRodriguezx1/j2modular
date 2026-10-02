<?php

namespace App\Repositories;

abstract class operationRepository extends BaseRepository{

    protected string $table;
    protected string $idsucursal;
    protected string $entityClass;  //entidad o modelo
    protected array $allowedColumns = [];


    public function insert(object $entity): array
    {
        $data = get_object_vars($entity);
        if(method_exists($entity, 'toArray'))$data = $entity->toArray();
        $cols = implode(', ', array_keys($data));
        $vals = implode("', '", array_map( fn($v)=>$this->escape((string)$v), array_values($data) ));
        $sql = "INSERT INTO {$this->table} ({$cols}) VALUES ('{$vals}');";
        $sql = str_replace("''", 'NULL', $sql);
        $resultado = self::$db->query($sql);
        if (!$resultado)throw new \Exception(self::$db->error);
        
        return [$resultado, self::$db->insert_id];  //insert_id retorna el ultimo registro insertado en la bd
           //  [true/false, id=1,2,3...00] = [0,1] 
    }


    public function crear_varios_reg_arrayobj(array $arrays = []):array{  // $arrays = [{'col1':a, 'col2':b}, {'col1':c, 'col2':d}, ...] 
        $string2 = '';

        foreach($arrays as $key => $obj){
            // si viene como stdClass → convertir a array
        //if($obj instanceof \stdClass)
            $array = (array)$obj;
            //crear entidad tipada
            $model = new $this->entityClass($array);
            $row = [];
            //mapeo
            if($this->allowedColumns){
                foreach($this->allowedColumns as $colDB)
                    $row[$colDB] = $model->$colDB??null;
            }else{
                foreach($model as $llave => $colDB){
                    if($llave == 'id' || $llave == 'created_at')continue;
                    $row[$llave] = $model->$llave??null;
                }
            }
            
            //tomar el arreglo de valores del objeto instanciado y se sanatiza
            $datos = array_map( fn($v)=>$this->escape((string)$v), array_values($row) );

            if(array_key_last($arrays) == $key){
                $string2 .= "('".join("', '", $datos)."');";
            }else{
                $string2 .= "('".join("', '", $datos)."'), ";
            }
        }
        $string2 = str_replace("'NULL'", 'NULL', $string2);
        $string1 = join(', ', array_keys($row));
        $sql = "INSERT INTO ".$this->table."(".$string1.") VALUES".$string2;
        $resultado = self::$db->query($sql);
        //INSERT INTO empserv(idempleado, idservicio) VALUES('3', '3'), ('3', '1');

        ///  calcular todos los IDs  ///
        //primer ID generado
        $primerId = self::$db->insert_id;
        $ids = [];
        for($i = 0; $i < count($arrays); $i++)$ids[] = $primerId + $i;
        return [$resultado, $ids];
    }


    public function update(object $entity):bool{
        $data = get_object_vars($entity);
        if(method_exists($entity, 'toArray'))$data = $entity->toArray();
        $sets = [];
        // La PK nunca se toma como expresion SQL; se normaliza a entero.
        $id = (int)$entity->id;
        $query = "UPDATE {$this->table} SET ";
        foreach( $data as $key => $value){
            if($value === null || $value == ''){
                $sets[] = "{$key}=NULL";
                continue;
            }
            // Los valores de formularios llegan como string. Aunque contengan
            // solo digitos (telefono, identificacion), deben conservar ceros
            // iniciales y signos; solo los tipos PHP numericos van sin comillas.
            if(is_int($value) || is_float($value)){
                $sets[] = "{$key}={$value}";
                continue;
            }
            // Los repositorios reciben entidades hidratadas desde formularios;
            // escapar aqui protege todos los UPDATE contra comillas e inyeccion.
            $value = $this->escape((string)$value);
            $sets[] = "{$key}='{$value}'";
        }
        $query .= implode(', ', $sets);
        $query .= " WHERE id = {$id} LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }


    public function upsert(object $entity): array{
        $data = get_object_vars($entity);
        if(method_exists($entity, 'toArray'))$data = $entity->toArray();

        $cols = array_keys($data);
        $vals = array_map(fn($v) => $v === null?'NULL':"'".$this->escape((string)$v)."'", array_values($data));
        $updates = [];

        foreach ($cols as $col) {
            if ($col === 'id')continue; // normalmente no actualizamos la PK
            $updates[] = "{$col} = VALUES({$col})";
        }

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s",
            $this->table,
            implode(', ', $cols),
            implode(', ', $vals),
            implode(', ', $updates)
        );

        $resultado = self::$db->query($sql);
        if(!$resultado)throw new \Exception(self::$db->error);
        return [$resultado, self::$db->insert_id];
    }


    public function updatemultiregobj(array $array=[], array $colums=[]):bool{
        $query = "UPDATE {$this->table} SET ";
        $in = '';
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
        }
        //debuguear($query);
        return self::$db->query($query);
    }


    public function delete(int $id): bool
    {
        return self::$db->query("DELETE FROM {$this->table} WHERE id = {$id} LIMIT 1");
    }


    public function delete_regs(string $id, $array = []): bool
    {
        return self::$db->query("DELETE FROM {$this->table} WHERE {$id} IN (".join(', ', $array).")");
    }


    public function all(): array
    {
        $rows = $this->fetchAll("SELECT * FROM {$this->table}");
        return array_map(fn($r) => new $this->entityClass($r), $rows);
    }


    public function get(int $cantidad):array{
        return $this->fetchAllStd("SELECT * FROM {$this->table} LIMIT $cantidad");
    }


    public function find(int $id): ?object
    {
        $rows = $this->fetchAll("SELECT * FROM {$this->table} WHERE id = {$id} LIMIT 1");
        return $rows ? new $this->entityClass($rows[0]) : null;
    }


    /** Obtiene y bloquea un registro por su llave primaria. */
    public function findForUpdate(int $id): ?object
    {
        if($id <= 0)return null;
        $rows = $this->fetchAll("SELECT * FROM {$this->table} WHERE id = {$id} LIMIT 1 FOR UPDATE");
        return $rows ? new $this->entityClass($rows[0]) : null;
    }


    /** Obtiene y bloquea los registros que cumplen todas las condiciones. */
    public function whereForUpdate(array $condiciones = []):array
    {
        if(empty($condiciones))throw new \InvalidArgumentException('Debe especificar condiciones para bloquear registros.');

        $where = [];
        foreach($condiciones as $columna => $valor){
            if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', (string)$columna))
                throw new \InvalidArgumentException('Columna no válida para bloquear registros.');
            $where[] = $columna." = '".$this->escape((string)$valor)."'";
        }

        $rows = $this->fetchAll(
            "SELECT * FROM {$this->table} WHERE ".implode(' AND ', $where)." FOR UPDATE"
        );
        return array_map(fn($row)=>new $this->entityClass($row), $rows);
    }


    public function findAll(string $col, int $id, string $orden = "ASC"): ?array  //similar a idregistros
    {
        $rows = $this->fetchAll("SELECT * FROM {$this->table} WHERE $col = {$id} ORDER BY id $orden;");
        return $rows ?  array_map(fn($r) => new $this->entityClass($r), $rows) : [];
    }

    public function where(array $array = [], string $orden = "ASC"): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE ";
        foreach($array as $key => $value){
            if(array_key_last($array) == $key){
                $sql.= " {$key} = '{$value}'";
            }else{
                $sql.= " {$key} = '{$value}' AND ";
            }
        }
        $sql .= " ORDER BY id $orden;";
        $rows = $this->fetchAll($sql);
        return array_map(fn($r) => new $this->entityClass($r), $rows);
    }


    public function uniqueWhere(array $array = []):object|null
    {
        $sql = "SELECT * FROM {$this->table} WHERE ";
        foreach($array as $key => $value){
            if(array_key_last($array) == $key){
                $sql.= " {$key} = '{$value}'";
            }else{
                $sql.= " {$key} = '{$value}' AND ";
            }
        }
        $rows = $this->fetchAll($sql);
        return $rows? new $this->entityClass($rows[0]): null;
    }


    public function IN_Where(string $colum, array $array = [], array $filter=[]):array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$colum} IN(";
        foreach($array as $key => $value){
            if(array_key_last($array) == $key){
                $sql.= "{$value}) AND $filter[0] = $filter[1];";
            }else{
                 $sql.= "{$value}, ";
            }
        }
        $rows = $this->fetchAll($sql);
        return $rows ? array_map(fn($r) => new $this->entityClass($r), $rows): [];
    }


    public function whereArrayBETWEEN(string $colum, string $fechaini, string $fechafin, array $array = []):array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$colum} BETWEEN $fechaini AND $fechafin AND";
        foreach($array as $key => $value){
            if(array_key_last($array) == $key){
                $sql.= " {$key} = '{$value}';";
            }else{
                $sql.= " {$key} = '{$value}' AND ";
            }
        }
         $rows = $this->fetchAllStd($sql);
        return $rows;
    }


    public function unJoinWhereArrayObj(string $targetTable, string $fkLocal, string $fkTarget, array $where = []):array{
        $sql = "SELECT {$this->table}.*, {$targetTable}.*, {$this->table}.id AS ID 
                FROM {$this->table} 
                JOIN {$targetTable} ON {$this->table}.{$fkLocal} = {$targetTable}.{$fkTarget}";

        if (!empty($where)) {
            $sql .= " WHERE";
            foreach ($where as $key => $value) {
                if(array_key_last($where) == $key){
                    $sql.= " ${key} = '${value}';";
                }else{
                    $sql.= " ${key} = '${value}' AND ";
                }
            }
        }
        $rows = $this->fetchAllStd($sql);
        return $rows;
    }

    public function querySQL(string $query):array{
        return $this->fetchAll($query);
    }

    
    public function getNumOrden(int $idsucursal): int {
        $sql = "SELECT COALESCE(MAX(num_orden), 0)+1 AS next_num FROM {$this->table} WHERE {$this->idsucursal} = $idsucursal;";
        $resultado = self::$db->query($sql);
        $r = $resultado->fetch_assoc();
        $resultado->free();
        return array_shift($r);
    }
    
}
