<?php

namespace App\Controller\Api;

use App\Service\OrderDetailService;
use App\Dto\OrderDetailDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        try {
            $orderDetails = $this->orderDetailService->getAllOrderDetails();
            $orderDetailDtos = array_map(
                fn($orderDetail) => new OrderDetailDto($orderDetail),
                $orderDetails
            );

            return $this->json($orderDetailDtos);
        } catch (\Throwable $e) {
            return $this->json(['message' => 'Unable to fetch order details', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        try {
            $orderDetail = $this->orderDetailService->getOrderDetailById($id);

            if (!$orderDetail) {
                return $this->json(['message' => 'Order detail not found'], 404);
            }

            return $this->json(new OrderDetailDto($orderDetail));
        } catch (\Throwable $e) {
            return $this->json(['message' => 'Unable to fetch order detail', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $orderDetail = $this->orderDetailService->createOrderDetail($data);
            $em->persist($orderDetail);
            $em->flush();

            return $this->json(new OrderDetailDto($orderDetail), 201);
        } catch (\JsonException $e) {
            return $this->json(['message' => 'Invalid JSON payload', 'error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return $this->json(['message' => 'Unable to create order detail', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $orderDetail = $this->orderDetailService->updateOrderDetail($id, $data);
            $em->flush();

            return $this->json(new OrderDetailDto($orderDetail));
        } catch (\JsonException $e) {
            return $this->json(['message' => 'Invalid JSON payload', 'error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return $this->json(['message' => 'Unable to update order detail', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $this->orderDetailService->deleteOrderDetail($id);
            $em->flush();

            return $this->json(['message' => 'Order detail deleted']);
        } catch (\Throwable $e) {
            return $this->json(['message' => 'Unable to delete order detail', 'error' => $e->getMessage()], 500);
        }
    }
}
