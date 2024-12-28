<?php

namespace App\Service;

use App\Entity\Cart;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{
    private CartRepository $cartRepository;
    private EntityManagerInterface $entityManager;

    private UserService $userService;

    private ProductOptionService $productOptionService;
    


    public function __construct(CartRepository $cartRepository, EntityManagerInterface $entityManager, UserService $userService, ProductOptionService $productOptionService)
    {
        $this->cartRepository = $cartRepository;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->productOptionService = $productOptionService;
    }

    public function getAllCartItems(): array
    {
        return $this->cartRepository->findAll();
    }

    public function getCartItemById(int $id): ?Cart
    {
        return $this->cartRepository->find($id);
    }

    public function createCartItem(array $data): Cart
    {
        $cart = new Cart();
        $user = $this->userService->getUserById($data['userId']);
        $productOption = $this->productOptionService->getProductOptionById($data['productOptionId']);
        $cart->setUser($user)
             ->setProductOption($productOption)
             ->setQuantity($data['quantity'] ?? 1)
             ->setCreatedAt(new \DateTime());

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return $cart;
    }

    public function updateCartItem(int $id, array $data): Cart
    {
        $cart = $this->getCartItemById($id);

        if (!$cart) {
            throw new \Exception('Cart item not found');
        }

        $cart->setQuantity($data['quantity'] ?? $cart->getQuantity());

        $this->entityManager->flush();

        return $cart;
    }

    public function deleteCartItem(int $id): void
    {
        $cart = $this->getCartItemById($id);

        if (!$cart) {
            throw new \Exception('Cart item not found');
        }

        $this->entityManager->remove($cart);
        $this->entityManager->flush();
    }
}
