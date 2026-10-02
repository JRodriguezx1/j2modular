<?php

namespace App\Core\Tenant;

class TenantResolver{
    public function resolve(): string{
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $parts = explode('.', $host);
        return $parts[0] ?? '';
    }
    
}