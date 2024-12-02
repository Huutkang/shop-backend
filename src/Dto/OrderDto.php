<?php

namespace App\Dto;

use App\Entity\Order;

class OrderDto
{
    public int $id;
    public int $userId;
    public float $totalAmount;
    public string $paymentMethod;
    public string $shippingStatus;
    public string $paymentStatus;
    public float $shippingFee;
    public float $discount;
    public ?int $couponId;
    public \DateTime $createdAt;
    public \DateTime $updatedAt;

    public function __construct(Order $order)
    {
        $this->id = $order->getId();
        $this->userId = $order->getUser()->getId();
        $this->totalAmount = $order->getTotalAmount();
        $this->paymentMethod = $order->getPaymentMethod();
        $this->shippingStatus = $order->getShippingStatus();
        $this->paymentStatus = $order->getPaymentStatus();
        $this->shippingFee = $order->getShippingFee();
        $this->discount = $order->getDiscount();
        $this->couponId = $order->getCoupon() ? $order->getCoupon()->getId() : null;
        $this->createdAt = $order->getCreatedAt();
        $this->updatedAt = $order->getUpdatedAt();
    }
}
