<?php

namespace App\Service;

use App\Entity\OrderDetail;
use App\Entity\Cart;
use App\Entity\Order;
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

    public function createOrderDetail(Cart $cart, Order $order): OrderDetail
    {
        $orderDetail = new OrderDetail();
        $productOption = $cart->getProductOption();
        if ($productOption->getStock() < $cart->getQuantity()){
            throw new \Exception('Sản phẩm đã hết hàng');
        }
        $productOption->setStock($productOption->getStock() - $cart->getQuantity()); // Cập nhật số lượng sản phẩm còn lại
        
        $orderDetail->setOrder($order)
            ->setProduct($productOption->getProduct())
            ->setName($productOption->getProduct()->getName()) // Lấy tên sản phẩm
            ->setQuantity($cart->getQuantity())
            ->setPrice($productOption->getPrice())
            ->setAttribute(null) // Thuộc tính có thể thêm sau
            ->setUrl(null); // URL hình ảnh hoặc liên kết có thể thêm sau

        $this->entityManager->persist($orderDetail);

        return $orderDetail;
    }

}
