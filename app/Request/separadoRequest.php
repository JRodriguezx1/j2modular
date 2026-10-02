<?php 

namespace App\Request;

//
class separadoRequest {
    protected array $data;
    protected array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }


    public function validate(): array
    {
        if (empty($this->data['cliente_id'])) {
            $this->errors['error'][] = "Debe seleccionar un cliente";
        }
        if ($this->data['capital'] <= 0) {
            $this->errors['error'][] = "el valor del credito total no es valido.";
        }
        if(!is_numeric($this->data['abonoinicial']))$this->errors['error'][] = "El abono inicial debe ser de tipo numerico.";
        
        if ($this->data['cantidadcuotas'] <= 0) {
            $this->errors['error'][] = "Numero de cuotas no es valido, verifica nuevamente.";
        }
        if ($this->data['montocuota'] <= 0) {
            $this->errors['error'][] = "Valor de la cuota no es valido.";
        }
        if ($this->data['montototal'] <= 0) {
            $this->errors['error'][] = "El capital + interes no es valido.";
        }
        /*if (!$this->data['frecuenciapago']) {
            $this->errors['error'][] = "Seleccionar dia de pago de la cuota.";
        }*/

        return $this->errors;
    }

}