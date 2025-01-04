<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    private OrderRepository $orderRepository;
    private EntityManagerInterface $entityManager;
    private UserService $userService;



    public function __construct(OrderRepository $orderRepository, EntityManagerInterface $entityManager, UserService $userService)
    {
        $this->orderRepository = $orderRepository;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
    }

    public function getAllOrders(): array
    {
        return $this->orderRepository->findAll();
    }

    public function getOrderById(int $id): ?Order
    {
        return $this->orderRepository->find($id);
    }

    public function createOrder(User $user, array $data): Order
    {
        $order = new Order();
        $order->setUser($user)
              ->setTotalAmount($data['totalAmount'] ?? throw new \Exception('Total amount is required'))
              ->setPaymentMethod($data['paymentMethod'] ?? throw new \Exception('Payment method is required'))
              ->setShippingStatus($data['shippingStatus'] ?? 'pending')
              ->setPaymentStatus($data['paymentStatus'] ?? 'pending')
              ->setShippingFee($data['shippingFee'] ?? 0.00)
              ->setDiscount($data['discount'] ?? 0.00)
              ->setCoupon($data['coupon'] ?? null)
              ->setCreatedAt(new \DateTime())
              ->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($order);
        $this->entityManager->flush();
      
        return $order;
    }

    public function updateOrder(int $id, array $data): Order
    {
        $order = $this->getOrderById($id);

        if (!$order) {
            throw new \Exception('Order not found');
        }

        $order->setTotalAmount($data['totalAmount'] ?? $order->getTotalAmount())
              ->setPaymentMethod($data['paymentMethod'] ?? $order->getPaymentMethod())
              ->setShippingStatus($data['shippingStatus'] ?? $order->getShippingStatus())
              ->setPaymentStatus($data['paymentStatus'] ?? $order->getPaymentStatus())
              ->setShippingFee($data['shippingFee'] ?? $order->getShippingFee())
              ->setDiscount($data['discount'] ?? $order->getDiscount())
              ->setCoupon($data['coupon'] ?? $order->getCoupon())
              ->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $order;
    }

    public function deleteOrder(int $id): void
    {
        $order = $this->getOrderById($id);

        if (!$order) {
            throw new \Exception('Order not found');
        }

        $this->entityManager->remove($order);
        $this->entityManager->flush();
    }
}
