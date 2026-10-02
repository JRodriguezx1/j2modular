<?php 

namespace App\services;


class paymentService {

    private object $repository;

    public function __construct(object $repo){
        $this->repository = $repo;
    }
    
    public function registrarPagos(array $mediospago, int $id){
        /*$modelo = $this->modelomediopago;
        $instance = [];
        foreach($mediospago as $objStd){
            //if($obj->mediopago_id == 1){
            //$ultimocierre->ventasenefectivo =  $ultimocierre->ventasenefectivo + $obj->valor;
            //}
            $objStd = new $modelo((array)$objStd);
            $objStd->pagoDestino($id);
            $instance[] = $objStd;
        }
        $registro = new $modelo();
        $registro->crear_varios_reg_arrayobj($instance);*/

        //$repo = $this->repository;
        $instance = [];
        $pagodestino = $this->repository->getPagoDestino();
        $entityClass = $this->repository->getEntityClass();
        foreach($mediospago as $objStd){
            $objStd = new $entityClass((array)$objStd);
            $objStd->$pagodestino = $id;
            $instance[] = $objStd;
        }
        $this->repository->crear_varios_reg_arrayobj($instance);

        
    }
}