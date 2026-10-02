<?php

namespace App\Repositories\clientes;

use App\Models\clientes\clientes;
use App\Repositories\operationRepository;

/**
 * Centraliza la persistencia y las consultas propias del agregado Cliente.
 * El modelo sigue heredando ActiveRecord durante la migracion, pero este
 * repositorio es la unica puerta de acceso usada por el modulo de Clientes.
 */
class clientesRepository extends operationRepository
{
    protected string $table = 'clientes';
    protected string $entityClass = clientes::class;
    protected array $allowedColumns = [];

    /**
     * Construye el repositorio y obtiene del modelo la lista blanca de columnas.
     * Se ejecuta al instanciarlo desde clientesService, clientescontrolador y
     * direccionescontrolador; no corresponde directamente a una accion del front.
     */
    public function __construct()
    {
        $this->allowedColumns = array_keys((new $this->entityClass())->toArray());
    }

    /**
     * Expone la conexion compartida para coordinar una transaccion del agregado.
     * Lo llaman clientesService::crearCliente() y ::actualizarCliente(), iniciados
     * desde clientes.ts (formCrearUpdateCliente) o ahelper.clientes.ts
     * (formAddCliente), para atomizar cliente y direccion en un solo commit.
     */
    public function getConexion()
    {
        return self::getDB();
    }

    /**
     * Busca una identificacion duplicada y, durante una edicion, excluye al
     * cliente actual. La llaman clientesService::crearCliente() y
     * ::actualizarCliente(). Esos flujos nacen en clientes.ts desde
     * formCrearUpdateCliente y en ahelper.clientes.ts desde formAddCliente.
     */
    public function identificacionExiste(?string $identificacion, ?int $exceptoId = null):bool
    {
        $identificacion = trim((string)$identificacion);
        if($identificacion === '')return false;

        $identificacionSql = $this->escape($identificacion);
        $sql = "SELECT id FROM {$this->table} WHERE identificacion = '{$identificacionSql}'";
        if($exceptoId !== null)$sql .= ' AND id <> '.(int)$exceptoId;
        $sql .= ' LIMIT 1';
        return !empty($this->fetchAll($sql));
    }

    /**
     * Filtra clientes por una columna permitida, evitando recibir nombres de
     * columnas arbitrarios. Lo llama direccionescontrolador::index() al procesar
     * un filtro POST; actualmente esa accion no tiene una ruta registrada en
     * public/index.php, por lo que queda preparada para ese flujo administrativo.
     */
    public function buscar(string $campo, string $termino):array
    {
        $permitidos = ['nombre', 'apellido', 'identificacion', 'telefono', 'email'];
        if(!in_array($campo, $permitidos, true))return $this->all();

        $termino = $this->escape(trim($termino));
        $rows = $this->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$campo} LIKE '%{$termino}%' ORDER BY id DESC"
        );
        return array_map(fn(array $row)=>new $this->entityClass($row), $rows);
    }

    /**
     * Calcula cantidad de ventas, total vendido y ticket promedio del cliente.
     * Lo llama clientescontrolador::detalle(); el flujo comienza al abrir el
     * enlace "Ver estadisticas" de views/admin/clientes/index.php.
     */
    public function indicadoresVentas(int $idCliente, int $idSucursal):?object
    {
        $sql = "SELECT COUNT(f.id) AS cantidad_ventas,
                       SUM(f.total) AS total_ventas_cliente,
                       AVG(f.total) AS ticket_promedio
                FROM facturas f
                WHERE f.fechapago >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                  AND f.id_sucursal = ".(int)$idSucursal."
                  AND f.idcliente = ".(int)$idCliente."
                  AND f.estado = 'Paga'";
        $rows = $this->fetchAllStd($sql);
        return $rows[0] ?? null;
    }

    /**
     * Agrupa por mes las compras pagadas del cliente durante los ultimos meses.
     * Lo llama clientescontrolador::comprasXMesXCliente() desde su API; el front
     * la solicita en detalle.ts mediante clientesGraficas(..., 'comprasXMes').
     */
    public function comprasPorMes(int $idCliente, int $idSucursal):array
    {
        $sql = "SELECT DATE_FORMAT(f.fechapago, '%Y-%m') AS periodo,
                       SUM(f.total) AS ventas_totales
                FROM facturas f
                WHERE f.fechapago >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                  AND f.id_sucursal = ".(int)$idSucursal."
                  AND f.idcliente = ".(int)$idCliente."
                  AND f.estado = 'Paga'
                GROUP BY DATE_FORMAT(f.fechapago, '%Y-%m')
                ORDER BY periodo";
        return $this->fetchAllStd($sql);
    }

    /**
     * Agrupa las unidades y el valor vendido al cliente por categoria.
     * Lo llama clientescontrolador::ventasXCategoriasXCliente() desde su API; el
     * front la solicita en detalle.ts con clientesGraficas(..., 'ventasXCategorias').
     */
    public function ventasPorCategoria(int $idCliente, int $idSucursal):array
    {
        $sql = "SELECT p.idcategoria,
                       c.nombre AS categoria,
                       SUM(v.cantidad) AS unidades_vendidas,
                       SUM(v.total) AS venta_total_categoria
                FROM facturas f
                JOIN ventas v ON v.idfactura = f.id
                JOIN productos p ON p.id = v.idproducto
                LEFT JOIN categorias c ON c.id = p.idcategoria
                WHERE f.id_sucursal = ".(int)$idSucursal."
                  AND f.idcliente = ".(int)$idCliente."
                  AND f.estado = 'Paga'
                GROUP BY p.idcategoria, c.nombre
                ORDER BY venta_total_categoria DESC";
        return $this->fetchAllStd($sql);
    }
}
