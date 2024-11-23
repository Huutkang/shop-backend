<?php

namespace App\Controller\Api;

use App\Service\OrderDetailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;



#[Route('/api/order-details', name: 'order_detail_')]
class OrderDetailController extends AbstractController
{
    private OrderDetailService $orderDetailService;

    public function __construct(OrderDetailService $orderDetailService)
    {
        $this->orderDetailService = $orderDetailService;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $orderDetails = $this->orderDetailService->getAllOrderDetails();
        return $this->json($orderDetails);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $orderDetail = $this->orderDetailService->getOrderDetailById($id);
        if (!$orderDetail) {
            return $this->json(['message' => 'OrderDetail not found'], 404);
        }

        return $this->json($orderDetail);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $orderDetail = $this->orderDetailService->createOrderDetail($data);
            $em->persist($orderDetail);
            $em->flush();

            return $this->json($orderDetail, 201);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $orderDetail = $this->orderDetailService->updateOrderDetail($id, $data);
            $em->flush();

            return $this->json($orderDetail);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $this->orderDetailService->deleteOrderDetail($id);
            $em->flush();

            return $this->json(['message' => 'OrderDetail deleted']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}
