<?php

namespace App\Controller\Api;

use App\Service\ReviewService;
use App\Dto\ReviewDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;



#[Route('/api/reviews', name: 'review_')]
class ReviewController extends AbstractController
{
    private ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $reviews = $this->reviewService->getAllReviews();
            $reviewDTOs = array_map(
                fn($review) => new ReviewDTO($review),
                $reviews
            );

            return $this->json($reviewDTOs);
        } catch (\Throwable $e) {
            return $this->json(['message' => 'Unable to fetch reviews', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        try {
            $review = $this->reviewService->getReviewById($id);

            if (!$review) {
                return $this->json(['message' => 'Review not found'], 404);
            }

            return $this->json(new ReviewDTO($review));
        } catch (\Throwable $e) {
            return $this->json(['message' => 'Unable to fetch review', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $review = $this->reviewService->createReview($data);

            return $this->json(new ReviewDTO($review), 201);
        } catch (\JsonException $e) {
            return $this->json(['message' => 'Invalid JSON payload', 'error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return $this->json(['message' => 'Unable to create review', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $review = $this->reviewService->updateReview($id, $data);

            return $this->json(new ReviewDTO($review));
        } catch (\JsonException $e) {
            return $this->json(['message' => 'Invalid JSON payload', 'error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return $this->json(['message' => 'Unable to update review', 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->reviewService->deleteReview($id);

            return $this->json(['message' => 'Review deleted']);
        } catch (\Throwable $e) {
            return $this->json(['message' => 'Unable to delete review', 'error' => $e->getMessage()], 500);
        }
    }
}
