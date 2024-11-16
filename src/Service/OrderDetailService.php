<?php

namespace App\Service;

use App\Entity\OrderDetail;
use App\Repository\OrderDetailRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderDetailService
{
    private OrderDetailRepository $orderDetailRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(OrderDetailRepository $orderDetailRepository, EntityManagerInterface $entityManager)
    {
        $this->orderDetailRepository = $orderDetailRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllOrderDetails(): array
    {
        return $this->orderDetailRepository->findAll();
    }

    public function getOrderDetailById(int $id): ?OrderDetail
    {
        return $this->orderDetailRepository->find($id);
    }

    public function createOrderDetail(array $data): OrderDetail
    {
        $orderDetail = new OrderDetail();
        $orderDetail->setOrder($data['order'] ?? throw new \Exception('Order is required'))
            ->setProduct($data['product'] ?? throw new \Exception('Product is required'))
            ->setQuantity($data['quantity'] ?? throw new \Exception('Quantity is required'))
            ->setPrice($data['price'] ?? throw new \Exception('Price is required'));

        return $orderDetail;
    }

    public function updateOrderDetail(int $id, array $data): OrderDetail
    {
        $orderDetail = $this->getOrderDetailById($id);

        if (!$orderDetail) {
            throw new \Exception('OrderDetail not found');
        }

        $orderDetail->setOrder($data['order'] ?? $orderDetail->getOrder())
            ->setProduct($data['product'] ?? $orderDetail->getProduct())
            ->setQuantity($data['quantity'] ?? $orderDetail->getQuantity())
            ->setPrice($data['price'] ?? $orderDetail->getPrice());

        return $orderDetail;
    }

    public function deleteOrderDetail(int $id): void
    {
        $orderDetail = $this->getOrderDetailById($id);

        if (!$orderDetail) {
            throw new \Exception('OrderDetail not found');
        }

        $this->entityManager->remove($orderDetail);
    }
}
