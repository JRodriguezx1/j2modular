<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

function validar_string_url($path):bool{ //retorna bolean
    //return strpos($_SERVER['PATH_INFO']??'/', $path)?true:false;
    return strpos($_SERVER['REQUEST_URI']??'/', $path)?true:false;
}

/** Exige una sesión autenticada y detiene la solicitud si no existe. */
function isauth():void{
  if(!isset($_SESSION['login'])){
      header('Location: /');
      exit;
  }
}

/** Exige una sesión con perfil administrativo válido. */
function isadmin():void{
    isauth();
    if(($_SESSION['perfil'] ?? null) === null){
        header('Location: /');
        exit;
    }
}

function nombreSucursal():string{
    return $_SESSION['sucursal']->nombre;
}

function id_sucursal():int{
    if(isset($_SESSION['idsucursal'])){
        return $_SESSION['idsucursal'];
    }else{
        return 1;
    }
}

function negocionSucursal():object{
    return $_SESSION['sucursal'];
    //$lineasencabezado = explode("\n", $sucursal->datosencabezados??'');
}

function tienePermiso(string $permiso): bool {
    return in_array($permiso, $_SESSION['permisos'] ?? []);
}

function userPerfil(): string|bool {
    return $_SESSION['perfil']??false;
}

function desactivarInterlacedPNG(string $rutaimg):int{
    // Detectar extensión
    $ext = strtolower(pathinfo($rutaimg, PATHINFO_EXTENSION));
    // Si es PNG, limpiarla con GD
    if ($ext === 'png') {
        $img = @imagecreatefrompng($rutaimg);
        if($img){
            // Desactivar interlazado
            imageinterlace($img, false);
            // Sobrescribir el archivo limpio
            imagepng($img, $rutaimg);
            // Liberar memoria
            imagedestroy($img);
            return 1;
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}

function buscarClaveArray(array $array, string $clave): bool|string|int|float|null {
    foreach ($array as $key => $value) {
        if($key === $clave){
            if(!is_array($value))return $value;
            return false;
        }
        if (is_array($value)) {
            $r = buscarClaveArray($value, $clave);
            if ($r !== false)return $r;
        } 
    }
    return false;
}


function getConfigLocal(): array {
    return $_SESSION['configLocal'] ?? [];
}
