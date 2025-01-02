<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    private NotificationRepository $repository;
    private EntityManagerInterface $entityManager;
    private UserService $userService;

    public function __construct(NotificationRepository $repository, EntityManagerInterface $entityManager, UserService $userService)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
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

    public function createNotification(array $data): Notification
    {
        $user = $this->userService->getUserById($data['userId']);
        $notification = new Notification($user);
        $notification->setTitle($data['title'])
                     ->setMessage($data['message']);

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

        $notification->setIsRead(true);
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
