<?php

namespace App\Core\Database;

use mysqli;

class MySqlTransactionManager implements TransactionManager
{
    public function __construct(private mysqli $db)
    {}

    public function begin(): void{
        if(!$this->db->begin_transaction())
            throw new \RuntimeException('No fue posible iniciar la transacción.'); 
    }

    public function commit(): void{
        if(!$this->db->commit())
            throw new \RuntimeException('No fue posible confirmar la transacción.');
    }

    public function rollback(): void{
        $this->db->rollback();
    }
    
}