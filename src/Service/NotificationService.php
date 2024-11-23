<?php

namespace App\Service;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    private NotificationRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(NotificationRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function getAllNotifications(): array
    {
        return $this->repository->findAllNotifications();
    }

    public function getUnreadNotifications(): array
    {
        return $this->repository->findUnreadNotifications();
    }

    public function markAllAsRead(): int
    {
        return $this->repository->markAllAsRead();
    }

    public function deleteReadNotifications(): int
    {
        return $this->repository->deleteReadNotifications();
    }

    public function getNotificationById(int $id): ?Notification
    {
        return $this->repository->find($id);
    }

    public function createNotification(string $title, ?string $message): Notification
    {
        $notification = new Notification();
        $notification->setTitle($title)
                     ->setMessage($message);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }

    public function markAsRead(int $id): ?Notification
    {
        $notification = $this->getNotificationById($id);
        if (!$notification) {
            throw new \Exception('Notification not found');
        }

        $notification->setRead(true);
        $this->entityManager->flush();

        return $notification;
    }

    public function deleteNotification(int $id): void
    {
        $notification = $this->getNotificationById($id);
        if (!$notification) {
            throw new \Exception('Notification not found');
        }

        $this->entityManager->remove($notification);
        $this->entityManager->flush();
    }
}
