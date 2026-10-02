<?php

namespace App\Repositories\clientes;

use App\Models\clientes\preciosporcliente;
use App\Repositories\operationRepository;

/** Persistencia de precios personalizados asociados a un cliente y producto. */
class preciosPorClienteRepository extends operationRepository
{
    protected string $table = 'preciosporcliente';
    protected string $entityClass = preciosporcliente::class;
    protected array $allowedColumns = [];

    public function __construct()
    {
        $this->allowedColumns = array_keys((new $this->entityClass())->toArray());
    }

    public function findPorClienteProducto(int $idCliente, int $idProducto):?preciosporcliente
    {
        return $this->uniqueWhere(['idcliente'=>$idCliente, 'idproducto'=>$idProducto]);
    }

    public function eliminarPorClienteProducto(int $idCliente, int $idProducto):bool
    {
        return self::$db->query(
            'DELETE FROM '.$this->table.
            ' WHERE idcliente = '.(int)$idCliente.
            ' AND idproducto = '.(int)$idProducto
        );
    }

    /** Une el precio con el nombre del producto para la vista administrativa. */
    public function conProductoPorCliente(int $idCliente):array
    {
        $sql = "SELECT ppc.*, p.nombre, p.unidadmedida
                FROM {$this->table} ppc
                JOIN productos p ON p.id = ppc.idproducto
                WHERE ppc.idcliente = ".(int)$idCliente."
                ORDER BY p.nombre";
        return $this->fetchAllStd($sql);
    }
}
