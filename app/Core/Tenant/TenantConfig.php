<?php

namespace App\Core\Tenant;

class TenantConfig{
    private array $tenants;

    public function __construct(){
        $this->tenants = require dirname(__DIR__, 3). '/config/tenants.php';
    }

    public function get(string $tenant): array{
        if (!isset($this->tenants[$tenant])) {
            throw new \RuntimeException("Tenant no configurado: {$tenant}");
        }
        return $this->tenants[$tenant];
    }
}