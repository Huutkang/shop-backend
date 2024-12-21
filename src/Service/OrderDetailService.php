<?php

namespace App\Service;

use App\Entity\OrderDetail;
use App\Repository\OrderDetailRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderDetailService
{
    private OrderDetailRepository $orderDetailRepository;
    private EntityManagerInterface $entityManager;
    private OrderService $orderService;
    private ProductService $productService;

    public function __construct(OrderDetailRepository $orderDetailRepository, EntityManagerInterface $entityManager, OrderService $orderService, ProductService $productService)
    {
        $this->orderDetailRepository = $orderDetailRepository;
        $this->entityManager = $entityManager;
        $this->orderService = $orderService;
        $this->productService = $productService;
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
        $order = $this->orderService->getOrderById($data['orderId']);
        $product = $this->productService->getProductById($data['productId']);
        $orderDetail->setOrder($order)
            ->setProduct($product)
            ->setQuantity($data['quantity'] ?? throw new \Exception('Quantity is required'))
            ->setPrice($data['price'] ?? throw new \Exception('Price is required'));

        $this->entityManager->persist($orderDetail);
        $this->entityManager->flush();
        
        return $orderDetail;
    }

    public function updateOrderDetail(int $id, array $data): OrderDetail
    {
        $orderDetail = $this->getOrderDetailById($id);

        if (!$orderDetail) {
            throw new \Exception('OrderDetail not found');
        }
        $order = $this->orderService->getOrderById($data['orderId']);
        $product = $this->productService->getProductById($data['productId']);
        $orderDetail->setOrder($order)
            ->setProduct($product)
            ->setQuantity($data['quantity'] ?? $orderDetail->getQuantity())
            ->setPrice($data['price'] ?? $orderDetail->getPrice());

        $this->entityManager->flush();

        return $orderDetail;
    }

    public function deleteOrderDetail(int $id): void
    {
        $orderDetail = $this->getOrderDetailById($id);

        if (!$orderDetail) {
            throw new \Exception('OrderDetail not found');
        }

        $this->entityManager->remove($orderDetail);
        $this->entityManager->flush();
    }
}
