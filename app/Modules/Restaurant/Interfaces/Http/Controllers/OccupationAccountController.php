<?php

namespace App\Modules\Restaurant\Interfaces\Http\Controllers;

use InvalidArgumentException;
use Throwable;

use App\Modules\Restaurant\Application\UseCases\GetOccupationAccounts;

class OccupationAccountController{

    public function __construct(private GetOccupationAccounts $getOccupationAccounts) {}

    public function index(): void{
        header('Content-Type: application/json; charset=utf-8');
        try {
            $occupationId = filter_input(INPUT_GET,  'occupationId', FILTER_VALIDATE_INT);
            if($occupationId === false || $occupationId === null || $occupationId <= 0)
                throw new InvalidArgumentException('El ID de la ocupación no es válido.');
            $result = $this->getOccupationAccounts->execute($occupationId);
            http_response_code(200);
            echo json_encode(['success' => true, 'data' => $result]);
        }catch(InvalidArgumentException $e){
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }catch(Throwable $e){
            // Registrar $e en el sistema de logs del proyecto.
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible consultar las cuentas.']);
        }
    }


    

}