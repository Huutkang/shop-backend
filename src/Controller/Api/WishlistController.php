<?php

namespace App\Controller\Api;

use App\Service\WishlistService;
use App\Dto\WishlistDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/wishlist', name: 'wishlist_')]
class WishlistController extends AbstractController
{
    private WishlistService $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $items = $this->wishlistService->getAllWishlistItems();
            $wishlistDTOs = array_map(
                fn($item) => new WishlistDTO($item),
                $items
            );

            return $this->json($wishlistDTOs);
        } catch (\Throwable $e) {
            return $this->json(['message' => 'Unable to fetch wishlist items', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        try {
            $item = $this->wishlistService->getWishlistItemById($id);

            if (!$item) {
                return $this->json(['message' => 'Wishlist item not found'], 404);
            }

            return $this->json(new WishlistDTO($item));
        } catch (\Throwable $e) {
            return $this->json(['message' => 'Unable to fetch wishlist item', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $item = $this->wishlistService->createWishlistItem($data);

            return $this->json(new WishlistDTO($item), 201);
        } catch (\JsonException $e) {
            return $this->json(['message' => 'Invalid JSON payload', 'error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return $this->json(['message' => 'Unable to create wishlist item', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->wishlistService->deleteWishlistItem($id);

            return $this->json(['message' => 'Wishlist item deleted']);
        } catch (\Throwable $e) {
            return $this->json(['message' => 'Unable to delete wishlist item', 'error' => $e->getMessage()], 500);
        }
    }
}
