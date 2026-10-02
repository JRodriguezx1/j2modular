<?php

namespace App\Models;

class empleados extends \App\Models\ActiveRecord {
    protected static $tabla = 'empleados';
    protected static $columnasDB = ['img', 'id', 'nombre', 'apellido', 'cedula', 'movil', 'email', 'fecha_nacimiento', 'genero', 'departamento', 'ciudad', 'direccion', 'perfil', 'timeservice', 'dato1', 'dato2'];
    protected static $avatar=['avatar/avatar.png', 'avatar/avatar1.png', 'avatar/avatar2.png', 'avatar/avatar3.png', 'avatar/avatar4.png', 'avatar/avatar5.png',
                                'avatar/avatar6.webp', 'avatar/avatar7.jpg', 'avatar/avatar8.jpg', 'avatar/avatar9.jpg', 'avatar/avatar10.jpg', 'avatar/avatar11.jpg',
                                'avatar/avatar12.jpg', 'avatar/avatar13.jpg', 'avatar/avatar14.jpg', 'avatar/avatar15.jpg', 'avatar/avatar16.jpg', 'avatar/avatar17.jpg',
                                'avatar/avatar18.jpg', 'avatar/avatar19.jpg', 'avatar/avatar20.jpg', 'avatar/avatar21.jpg', 'avatar/avatar22.jpg', 'avatar/avatar23.jpg',
                                'avatar/avatar24.jpg', 'avatar/avatar25.jpg', 'avatar/avatar26.jpg', 'avatar/avatar27.jpg', 'avatar/avatar28.jpg', 'avatar/avatar29.jpg'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->img = $args['img'] ?? self::$avatar[mt_rand(0, 29)];
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->cedula = $args['cedula'] ?? '';
        $this->movil = $args['movil'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->fecha_nacimiento = $args['fecha_nacimiento'] ?? date('Y-m-d');
        $this->genero = $args['genero'] ?? '';
        $this->departamento = $args['departamento'] ?? '';
        $this->ciudad = $args['ciudad'] ?? '';
        $this->direccion = $args['direccion'] ?? '';
        $this->perfil = $args['perfil'] ?? 1;
        $this->timeservice = $args['timeservice'] ?? '';
        $this->dato1 = $args['dato1'] ?? '';
        $this->dato2 = $args['dato2'] ?? '';
    }

    // Validar los servicios
    public function validarempleado() {
        if(!$this->nombre || strlen($this->nombre)<3) {
            self::$alertas['error'][] = 'Nombre no valido';  //['error] = ['string1', 'string2'...]
        }  //como el arreglo alertas es heredada de la clase padre activerecord self hace referencia a este arreglo de la clase padre
        if(!$this->apellido || strlen($this->apellido)<3) {
            self::$alertas['error'][] = 'apellido no valido';  
        }
        /*if(!$this->cedula || strlen($this->cedula)<6 || strlen($this->cedula)<10) {
            self::$alertas['error'][] = 'cedula no valido';  
        }*/
        if(!$this->movil || strlen($this->movil)<9) {
            self::$alertas['error'][] = 'movil no valido';  
        }
        return self::$alertas;
    }
    
    public function validarEmail() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        return self::$alertas;
    }

    public function validarimgempleado($FILE) {
        if($FILE['img']['name'] && $FILE['img']['size']>550000) {
            self::$alertas['error'][] = 'La foto no puede pasar los 500KB';
        }
        if($FILE['img']['name'] && $FILE['img']['type']!="image/jpeg" && $FILE['img']['type']!="image/png") {
            self::$alertas['error'][] = 'Seleccione una imagen en formato jpeg o png';
        }
        return self::$alertas;
    }
}