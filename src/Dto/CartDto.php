<?php

namespace App\Dto;

use App\Entity\Cart;

class CartDto
{
    public int $id;
    public int $quantity;
    public string $createdAt;
    public int $userId;
    public int $productOptionId;

    public function __construct(Cart $cart)
    {
        $this->id = $cart->getId();
        $this->quantity = $cart->getQuantity();
        $this->createdAt = $cart->getCreatedAt()->format('Y-m-d H:i:s');
        $this->userId = $cart->getUser()->getId();
        $this->productOptionId = $cart->getProductOption()->getId();
    }
}
