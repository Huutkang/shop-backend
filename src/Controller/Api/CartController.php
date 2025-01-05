<?php

namespace App\Controller\Api;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;
use App\Dto\CartDto;

#[Route('/api/cart', name: 'cart_')]
class CartController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/all', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $items = $this->cartService->getAllCartItems();
        $cartDtos = array_map(fn($item) => new CartDto($item), $items);
        return $this->json($cartDtos);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $item = $this->cartService->getCartItemById($id);

        if (!$item) {
            return $this->json(['message' => 'Cart item not found'], 404);
        }

        return $this->json(new CartDto($item));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {   
        $user = $request->attributes->get('user');
        if (!$user){
            throw new AppException('E2025');
        }

        $data = json_decode($request->getContent(), true);

        try {
            $item = $this->cartService->createCartItem($user, $data);
            return $this->json(new CartDto($item), 201);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $item = $this->cartService->updateCartItem($id, $data);
            return $this->json(new CartDto($item));
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->cartService->deleteCartItem($id);
            return $this->json(['message' => 'Cart item deleted']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('', name: 'user_cart', methods: ['GET'])]
    public function userCart(Request $request): JsonResponse
    {
        try {
            $user = $request->attributes->get('user');
            if (!$user){
                throw new AppException('E2025');
            }

            // Gọi service để lấy giỏ hàng của người dùng
            $cartItems = $this->cartService->getUserCart($user);

            // Chuyển đổi dữ liệu sang DTO trước khi trả về
            $cartDtos = array_map(fn($item) => new CartDto($item), $cartItems);

            return $this->json($cartDtos);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 500);
        }
    }
}
