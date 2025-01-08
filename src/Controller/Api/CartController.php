<?php

namespace App\Controller\Api;

use App\Service\CartService;
use App\Service\AuthorizationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;
use App\Dto\CartDto;
use App\Validators\CartValidator;

#[Route('/api/cart', name: 'cart_')]
class CartController extends AbstractController
{
    private CartService $cartService;
    private AuthorizationService $authorizationService;
    private CartValidator $cartValidator;

    public function __construct(CartService $cartService, AuthorizationService $authorizationService, CartValidator $cartValidator)
    {
        $this->cartService = $cartService;
        $this->authorizationService = $authorizationService;
        $this->cartValidator = $cartValidator;
    }

    #[Route('/all', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {   
        $userCurrent = $request->attributes->get('user');
        if (!$userCurrent){
            throw new AppException('E2025');
        }
        $a = $this->authorizationService->checkPermission($userCurrent, "view_carts");
        if (!$a) {
            throw new AppException('E2020');
        }
        $items = $this->cartService->getAllCartItems();
        $cartDtos = array_map(fn($item) => new CartDto($item), $items);
        return $this->json($cartDtos);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id, Request $request): JsonResponse
    {
        $item = $this->cartService->getCartItemById($id);
        $userCurrent = $request->attributes->get('user');
        if (!$userCurrent){
            throw new AppException('E2025');
        }
        $a = $this->authorizationService->checkPermission($userCurrent, "view_carts", $id);
        if ($a || $item->getUser() === $userCurrent) {
            if (!$item) {
                return $this->json(['message' => 'Cart item not found'], 404);
            }
            return $this->json(new CartDto($item)); 
        }
        else{
            throw new AppException('E2020');
        }
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {   
        $userCurrent = $request->attributes->get('user');
        if (!$userCurrent){
            throw new AppException('E2025');
        }
        $a = $this->authorizationService->checkPermission($userCurrent, "create_cart");
        if (!$a) {
            throw new AppException('E2021');
        }
        $user = $request->attributes->get('user');
        if (!$user){
            throw new AppException('E2025');
        }

        $data = json_decode($request->getContent(), true);
        $validatedData = $this->cartValidator->validateCartData($data, 'create');
        $item = $this->cartService->createCartItem($user, $validatedData);
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
        $userCurrent = $request->attributes->get('user');
        if (!$userCurrent){
            throw new AppException('E2025');
        }
        $data = json_decode($request->getContent(), true);
        $validatedData = $this->cartValidator->validateCartData($data, 'update');
        $validatedData['userCurrent'] = $userCurrent;
        try {
            // kiểm tra quyền phía service
            $item = $this->cartService->updateCartItem($id, $validatedData);
            return $this->json(new CartDto($item));
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, Request $request): JsonResponse
    {
        $userCurrent = $request->attributes->get('user');
        if (!$userCurrent){
            throw new AppException('E2025');
        }
        try {
            // kiểm tra quyền phía service
            $this->cartService->deleteCartItem($id, $userCurrent);
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
