<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\ProductOption;
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

    public function getUserCart(User $user): array
    {
        if (!$user) {
            throw new \Exception('User not found');
        }
        return $this->cartRepository->findCartItemsByUser($user);
    }

    public function getCartItemById(int $id): ?Cart
    {
        return $this->cartRepository->find($id);
    }

    public function getCartItemByIds(array $ids): array
    {
        return $this->cartRepository->findCartItemsByIds($ids);
    }

    public function createCartItem(User $user, array $data): Cart
    {
        $cart = new Cart();
        $productOption = $this->productOptionService->getProductOptionById($data['productOptionId']);
        $cart->setUser($user)
             ->setProductOption($productOption)
             ->setCreatedAt(new \DateTime());

        $quantity = $data['quantity'] ?? 1;
        if ($this->checkQuantity($productOption, $quantity)){
            $cart->setQuantity($quantity);
        }else{
            throw new \Exception('Quantity exceeds the stock');
        }
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return $cart;
    }

    public function checkQuantity(ProductOption $productOption, int $quantity): bool{
        if($productOption && $productOption->getStock() < $quantity){
            return false;
        }
        return true;
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
