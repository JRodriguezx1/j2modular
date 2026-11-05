<?php

namespace App\Modules\Restaurant\Interfaces\Http\Controllers;

use InvalidArgumentException;
use Throwable;

use App\Modules\Restaurant\Application\UseCases\GetOccupationAccounts;
use App\Modules\Restaurant\Application\UseCases\GetAccountSummary;
use App\Modules\Restaurant\Application\UseCases\SaveFirstTableOrder;
use App\Modules\Restaurant\Application\UseCases\AddProductsToAccount;
use App\Modules\Restaurant\Application\UseCases\UpdateAccountCustomer;

class OccupationAccountController{

    public function __construct(
        private GetOccupationAccounts $getOccupationAccounts, 
        private GetAccountSummary $getAccountSummary,
        private SaveFirstTableOrder $saveFirstTableOrder,
        private AddProductsToAccount $addProductsToAccount,
        private UpdateAccountCustomer $updateAccountCustomer
    ){}

    //llamada desdes occupationApi.ts, consultar las cuentas asociadas a la ocupacion
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
    

    // Primer pedido de una mesa libre: el caso de uso crea toda la atención.
    public function crearOrdenComanda(): void{
        header('Content-Type: application/json; charset=utf-8');
        if(($_SESSION['login'] ?? false) !== true){
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        try{
            $positiveId = ['options' => ['min_range' => 1]];
            // resourceId es recursos.id, no mesas.id.
            $resourceId = filter_var($_POST['resourceId'] ?? null, FILTER_VALIDATE_INT, $positiveId);
            // Estos datos vienen de la sesión, nunca del formulario.
            $branchId = filter_var($_SESSION['idsucursal'] ?? null, FILTER_VALIDATE_INT, $positiveId);
            $sellerId = filter_var($_SESSION['id'] ?? null, FILTER_VALIDATE_INT, $positiveId);
            if($resourceId === false || $branchId === false || $sellerId === false)
                throw new InvalidArgumentException('La mesa, la sucursal o el vendedor no es válido.');

            // El cliente puede omitirse o enviarse vacío.
            $customerId = $_POST['customerId'] ?? null;
            if($customerId === '')$customerId = null;
            if($customerId !== null){
                $customerId = filter_var($customerId, FILTER_VALIDATE_INT, $positiveId);
                if($customerId === false)
                    throw new InvalidArgumentException('El cliente no es válido.');
            }

            // El formulario envía items con JSON.stringify(items).
            $itemsJson = $_POST['items'] ?? null;
            $items = json_decode($itemsJson, true, 512, JSON_THROW_ON_ERROR);
            if(!is_array($items) || $items === [] || !array_is_list($items))
                throw new InvalidArgumentException('El pedido debe contener una lista de productos.');

            // La transacción y las reglas comerciales pertenecen al caso de uso.
            $result = $this->saveFirstTableOrder->execute($resourceId, $items, $branchId, $sellerId, $customerId);
            http_response_code(201);
            echo json_encode(['success' => true, 'data' => $result], JSON_UNESCAPED_UNICODE);
        }catch(InvalidArgumentException $e){
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }catch(Throwable $e){
            error_log('crearOrdenComanda: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible guardar el pedido.'], JSON_UNESCAPED_UNICODE);
        }
    }


    // Guarda productos pendientes en una cuenta que ya existe. llamada desde saveProductsToAccount de orderApi.ts
    public function addProducts(): void{
        $clientId = null;
        header('Content-Type: application/json; charset=utf-8');
        if(($_SESSION['login'] ?? false) !== true){
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $positiveId = ['options' => ['min_range' => 1]];
            // El frontend identifica la cuenta de destino.
            $accountId = filter_var($_POST['accountId']??null, FILTER_VALIDATE_INT, $positiveId);
            // Sucursal y vendedor siempre vienen de la sesión.
            $branchId = filter_var($_SESSION['idsucursal']??null, FILTER_VALIDATE_INT, $positiveId);
            $sellerId = filter_var($_SESSION['id']??null, FILTER_VALIDATE_INT, $positiveId);
            $changeCustomer = array_key_exists('clientId', $_POST);

            if($changeCustomer){
                if($_POST['clientId'] !== '' && $_POST['clientId'] !== null){
                    $clientId = filter_var($_POST['clientId'], FILTER_VALIDATE_INT, $positiveId);
                    if($clientId === false)throw new InvalidArgumentException('El cliente no es válido.');
                }
            }

            if($accountId === false || $branchId === false || $sellerId === false)
                throw new InvalidArgumentException('La cuenta, la sucursal o el vendedor no es válido.');

            // El frontend enviará items mediante JSON.stringify().
            $itemsJson = $_POST['items'] ?? null;
            if(!is_string($itemsJson) || trim($itemsJson) === '')
                throw new InvalidArgumentException('Debes enviar los productos pendientes.');
            $items = json_decode($itemsJson, true, 512, JSON_THROW_ON_ERROR);

            if(!is_array($items) || $items === [] || !array_is_list($items))
                throw new InvalidArgumentException('El pedido debe contener una lista de productos.');

            //El caso de uso controla la transacción y valida la cuenta. POS valida los productos, obtiene precios y guarda el lote.
            $result = $this->addProductsToAccount->execute($accountId, $items, $branchId, $sellerId, $clientId, $changeCustomer);
            http_response_code(201);
            echo json_encode(['success' => true, 'data' => $result], JSON_UNESCAPED_UNICODE);
        }catch(InvalidArgumentException $e){
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }catch(Throwable $e){
            error_log('AddProductsToAccount: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible guardar los productos.'], JSON_UNESCAPED_UNICODE);
        }
    }


    public function summary(): void{
        header('Content-Type: application/json; charset=utf-8');

        if(($_SESSION['login'] ?? false) !== true){
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        try{
            $accountId = filter_input(INPUT_GET, 'accountId', FILTER_VALIDATE_INT);
            $branchId = filter_var($_SESSION['idsucursal'] ?? null, FILTER_VALIDATE_INT);
            if(!is_int($accountId) || $accountId <= 0)
                throw new InvalidArgumentException('El ID de la cuenta no es válido.');

            if(!is_int($branchId) || $branchId <= 0)
                throw new InvalidArgumentException('La sucursal de la sesión no es válida.');

            $result = $this->getAccountSummary->execute($accountId, $branchId);
            http_response_code(200);
            echo json_encode(['success' => true, 'data' => $result], JSON_UNESCAPED_UNICODE);
        }catch (InvalidArgumentException $e){
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }catch(Throwable $e){
            error_log('GetAccountSummary: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible consultar la cuenta.'], JSON_UNESCAPED_UNICODE);
        }
    }


    // // Actualiza únicamente el cliente de la ocupación y de su factura.
    public function UpdateAccountCustomer(): void{
        $clientId = null;
        header('Content-Type: application/json; charset=utf-8');
        if(($_SESSION['login'] ?? false) !== true){
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $positiveId = ['options' => ['min_range' => 1]];
            $accountId = filter_var($_POST['accountId']??null, FILTER_VALIDATE_INT, $positiveId);
            $branchId = filter_var($_SESSION['idsucursal']??null, FILTER_VALIDATE_INT, $positiveId);
            // El campo debe estar presente: omitirlo no significa retirar cliente.
            if(!array_key_exists('clientId', $_POST))
                throw new InvalidArgumentException('Debes indicar el cliente que deseas asignar o retirar.');
            if($_POST['clientId'] !== '' && $_POST['clientId'] !== null){ // Vacío o null: retirar. Número positivo: asignar.
                $clientId = filter_var($_POST['clientId'], FILTER_VALIDATE_INT, $positiveId);
                if($clientId === false)throw new InvalidArgumentException('El cliente no es válido.');
            }

            if($accountId === false || $branchId === false)
                throw new InvalidArgumentException('La cuenta o sucursal no es válido.');

            $result = $this->updateAccountCustomer->execute($accountId, $branchId, $clientId);
            http_response_code(200);
            echo json_encode(['success' => true, 'data' => $result], JSON_UNESCAPED_UNICODE);
        }catch(InvalidArgumentException $e){
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }catch(Throwable $e){
            error_log('Actualiza únicamente el cliente de la ocupación y de su factura. ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'No fue posible actualizar el cliente.'], JSON_UNESCAPED_UNICODE);
        }
    }

}