<?php

namespace App\Controller\Api;

use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/notifications', name: 'notifications_')]
class NotificationController extends AbstractController
{
    private NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $notifications = $this->service->getAllNotifications();
            return $this->json($notifications);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        try {
            $notification = $this->service->getNotificationById($id);
            if (!$notification) {
                return $this->json(['message' => 'Notification not found'], 404);
            }
            return $this->json($notification);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 500);
        }
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $notification = $this->service->createNotification(
                $data['title'] ?? throw new \Exception('Title is required'),
                $data['message'] ?? null
            );
            return $this->json($notification, 201);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}/read', methods: ['PATCH'])]
    public function markAsRead(int $id): JsonResponse
    {
        try {
            $notification = $this->service->markAsRead($id);
            return $this->json($notification);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->service->deleteNotification($id);
            return $this->json(['message' => 'Notification deleted']);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}
