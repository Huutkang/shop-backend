<?php

namespace App\Service;

use App\Entity\Interaction;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Action;
use App\Exception\AppException;
use Doctrine\ORM\EntityManagerInterface;

class InteractionService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createInteraction(array $data): Interaction
    {
        $user = $this->entityManager->getReference(User::class, $data['userId'] ?? throw new AppException('User ID is required'));
        $product = $this->entityManager->getReference(Product::class, $data['productId'] ?? throw new AppException('Product ID is required'));
        $action = $this->entityManager->getReference(Action::class, $data['actionId'] ?? throw new AppException('Action ID is required'));

        $interaction = new Interaction();
        $interaction->setUser($user)
                    ->setProduct($product)
                    ->setAction($action);

        $this->entityManager->persist($interaction);
        $this->entityManager->flush();

        return $interaction;
    }

    public function getUserInteractions(int $userId): array
    {
        return $this->entityManager->getRepository(Interaction::class)->findByUserId($userId);
    }

    public function getProductInteractions(int $productId): array
    {
        return $this->entityManager->getRepository(Interaction::class)->findByProductId($productId);
    }

    public function countInteractionsByActionId(int $actionId): int
    {
        return $this->entityManager->getRepository(Interaction::class)->countByActionId($actionId);
    }
}
