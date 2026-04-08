<?php

namespace App\Order;

class OrderItem
{
    public ?int $id = null;
    public ?int $customerId = null;
    public string $orderName = '';
    public int $orderType = 1;  // 1 - repair, 2  - sell
    public string $orderNotes = '';
    public float $orderPrice = 0.0;
    
    public string $imei = '';
    public ?int $deviceType = null; // 1 - phone, 2 - ipad


}