<?php

namespace App\Dto;

use App\Entity\OrderDetail;

class OrderDetailDto
{
    public int $id;
    public int $orderId;
    public int $productId;
    public int $quantity;
    public float $price;

    public function __construct(OrderDetail $orderDetail)
    {
        $this->id = $orderDetail->getId();
        $this->orderId = $orderDetail->getOrder()->getId();
        $this->productId = $orderDetail->getProduct()->getId();
        $this->quantity = $orderDetail->getQuantity();
        $this->price = $orderDetail->getPrice();
    }
}
