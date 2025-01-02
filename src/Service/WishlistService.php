<?php

namespace App\Service;

use App\Entity\Wishlist;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManagerInterface;

class WishlistService
{
    private WishlistRepository $wishlistRepository;
    private EntityManagerInterface $entityManager;
    private UserService $userService;
    private ProductService $productService;


    public function __construct(WishlistRepository $wishlistRepository, EntityManagerInterface $entityManager, UserService $userService, ProductService $productService)
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->productService = $productService;
    }

    public function getAllWishlistItems(): array
    {
        return $this->wishlistRepository->findAll();
    }

    public function getWishlistItemById(int $id): ?Wishlist
    {
        return $this->wishlistRepository->find($id);
    }

    public function createWishlistItem(array $data): Wishlist
    {
        $wishlist = new Wishlist();
        $user = $this->userService->getUserById($data['userId']);
        $product = $this->productService->getProductById($data['productId']);
        $wishlist->setUser($user)
                 ->setProduct($product)
                 ->setCreatedAt(new \DateTime());

        $this->entityManager->persist($wishlist);
        $this->entityManager->flush();

        return $wishlist;
    }

    public function deleteWishlistItem(int $id): void
    {
        $wishlist = $this->getWishlistItemById($id);

        if (!$wishlist) {
            throw new \Exception('Wishlist item not found');
        }

        $this->entityManager->remove($wishlist);
        $this->entityManager->flush();
    }
}
