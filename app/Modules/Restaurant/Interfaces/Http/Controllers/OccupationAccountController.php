<?php

namespace App\Modules\Restaurant\Interfaces\Http\Controllers;

use InvalidArgumentException;
use Throwable;

use App\Modules\Restaurant\Application\UseCases\GetOccupationAccounts;
use App\Modules\Restaurant\Application\UseCases\AddProductToAccount;

class OccupationAccountController{

    public function __construct(private GetOccupationAccounts $getOccupationAccounts, private AddProductToAccount $addProductToAccount) {}

    public function index(): void{
        header('Content-Type: application/json; charset=utf-8');
        if(($_SESSION['login'] ?? false) !== true){
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $occupationId = filter_input(INPUT_GET, 'occupationId', FILTER_VALIDATE_INT);
            $branchId = filter_var($_SESSION['idsucursal'] ?? null, FILTER_VALIDATE_INT);

            if(!is_int($occupationId) || $occupationId <= 0)
                throw new InvalidArgumentException('El ID de la ocupación no es válido.');

            if(!is_int($branchId) || $branchId <= 0)
                throw new InvalidArgumentException('La sucursal de la sesión no es válida.');

            $result = $this->getOccupationAccounts->execute($occupationId, $branchId);
            http_response_code(200);
            echo json_encode(['success' => true, 'data' => $result], JSON_UNESCAPED_UNICODE);
        }catch(InvalidArgumentException $e){
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }catch(Throwable $e){
            // Registrar $e en el sistema de logs del proyecto.
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible consultar las cuentas.'], JSON_UNESCAPED_UNICODE);
        }
    }



    public function addProduct(): void{
        header('Content-Type: application/json; charset=utf-8');
        if(($_SESSION['login'] ?? false) !== true){
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $accountId = filter_var($_POST['accountId'] ?? null, FILTER_VALIDATE_INT);
            $productId = filter_var($_POST['productId'] ?? null, FILTER_VALIDATE_INT);
            $quantity = $_POST['quantity'] ?? null;
            $branchId = filter_var($_SESSION['idsucursal'] ?? null, FILTER_VALIDATE_INT);
            $sellerId = filter_var($_SESSION['id'] ?? null, FILTER_VALIDATE_INT);

            if($accountId === false || $productId === false || !is_string($quantity) || $branchId === false || $sellerId === false)
                throw new InvalidArgumentException('Los datos para agregar el producto no son válidos.');

            $result = $this->addProductToAccount->execute($accountId, $productId, $quantity, $branchId, $sellerId);
            http_response_code(201);
            echo json_encode(['success' => true, 'data' => $result], JSON_UNESCAPED_UNICODE);
        }catch(InvalidArgumentException $e){
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }catch(Throwable $e){
            error_log('AddProductToAccount: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible agregar el producto.'], JSON_UNESCAPED_UNICODE);
        }
    }
    

}
