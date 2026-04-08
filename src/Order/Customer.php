<?php

namespace App\Order;

class Customer
{
    private $id;
    private $name;
    private $mobile;
    private $address;

    public function getId():int
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }


    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function setMobile($mobile): self
    {
        $this->mobile = $mobile;
        return $this;
    }


    public function getAddress(): string
    {
        return $this->address;
    }
    public function setAddress($address): self
    {
        $this->address = $address;
        return $this;
    }
}