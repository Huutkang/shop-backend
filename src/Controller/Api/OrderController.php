<?php

namespace App\Controller\Api;

use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;
use App\Dto\OrderDto;

#[Route('/api/orders', name: 'order_')]
class OrderController extends AbstractController
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $orders = $this->orderService->getAllOrders();
            $orderDtos = array_map(fn($order) => new OrderDto($order), $orders);
            return $this->json($orderDtos);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                throw new AppException('Order not found', 404);
            }
            return $this->json(new OrderDto($order));
        } catch (AppException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getCode());
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 500);
        }
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $order = $this->orderService->createOrder($data);
            return $this->json(new OrderDto($order), 201);
        } catch (AppException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $order = $this->orderService->updateOrder($id, $data);
            return $this->json(new OrderDto($order));
        } catch (AppException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->orderService->deleteOrder($id);
            return $this->json(['message' => 'Order deleted']);
        } catch (AppException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 500);
        }
    }
}
