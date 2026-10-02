<?php

namespace App\Modules\Restaurant\Domain\Entities;

class Customer{

    public function __construct(private int $id, private string $name, private string $lastName, private ?string $identification, private ?string $phone, private ?string $email)
    {}


    public function getId(): int{
        return $this->id;
    }


    public function getName(): string{
        return $this->name;
    }


    public function getLastName(): string{
        return $this->lastName;
    }


    public function getFullName(): string{
        return trim($this->name . ' ' . $this->lastName);
    }


    public function getIdentification(): ?string{
        return $this->identification;
    }


    public function getPhone(): ?string{
        return $this->phone;
    }


    public function getEmail(): ?string{
        return $this->email;
    }
    
}