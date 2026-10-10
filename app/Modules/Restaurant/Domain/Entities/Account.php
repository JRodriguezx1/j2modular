<?php

namespace App\Modules\Restaurant\Domain\Entities;

class Account{

    public function __construct(private int $id, private int $occupationId, private ?int $invoiceId, private string $name, private string $status)
    {}

    public function getId(): int{
        return $this->id;
    }

    public function getOccupationId(): int{
        return $this->occupationId;
    }

    public function getInvoiceId(): ?int{
        return $this->invoiceId;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getStatus(): string{
        return $this->status;
    }
    
}