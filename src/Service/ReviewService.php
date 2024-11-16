<?php

namespace App\Service;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReviewService
{
    private ReviewRepository $reviewRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ReviewRepository $reviewRepository, EntityManagerInterface $entityManager)
    {
        $this->reviewRepository = $reviewRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllReviews(): array
    {
        return $this->reviewRepository->findAll();
    }

    public function getReviewById(int $id): ?Review
    {
        return $this->reviewRepository->find($id);
    }

    public function createReview(array $data): Review
    {
        $review = new Review();
        $review->setUserId($data['userId'] ?? throw new \Exception('User ID is required'))
               ->setProductId($data['productId'] ?? throw new \Exception('Product ID is required'))
               ->setRating($data['rating'] ?? throw new \Exception('Rating is required'))
               ->setComment($data['comment'] ?? null)
               ->setCreatedAt(new \DateTime());

        $this->entityManager->persist($review);
        $this->entityManager->flush();

        return $review;
    }

    public function updateReview(int $id, array $data): Review
    {
        $review = $this->getReviewById($id);

        if (!$review) {
            throw new \Exception('Review not found');
        }

        $review->setRating($data['rating'] ?? $review->getRating())
               ->setComment($data['comment'] ?? $review->getComment())
               ->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $review;
    }

    public function deleteReview(int $id): void
    {
        $review = $this->getReviewById($id);

        if (!$review) {
            throw new \Exception('Review not found');
        }

        $this->entityManager->remove($review);
        $this->entityManager->flush();
    }
}
