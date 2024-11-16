<?php

namespace App\Service;

use App\Entity\Wishlist;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManagerInterface;

class WishlistService
{
    private WishlistRepository $wishlistRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(WishlistRepository $wishlistRepository, EntityManagerInterface $entityManager)
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->entityManager = $entityManager;
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
        $wishlist->setUserId($data['userId'] ?? throw new \Exception('User ID is required'))
                 ->setProductId($data['productId'] ?? throw new \Exception('Product ID is required'))
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
