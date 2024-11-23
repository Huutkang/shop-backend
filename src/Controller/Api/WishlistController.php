<?php

namespace App\Controller\Api;

use App\Service\WishlistService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;



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
        $items = $this->wishlistService->getAllWishlistItems();
        return $this->json($items);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $item = $this->wishlistService->getWishlistItemById($id);
        if (!$item) {
            return $this->json(['message' => 'Wishlist item not found'], 404);
        }

        return $this->json($item);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $item = $this->wishlistService->createWishlistItem($data);
            return $this->json($item, 201);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->wishlistService->deleteWishlistItem($id);
            return $this->json(['message' => 'Wishlist item deleted']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}
