<?php

namespace App\Controller\Api;

use App\Service\UserService;
use App\Service\AuthenticationService;
use App\Service\AuthorizationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\AppException;



class SecurityController extends AbstractController
{
    private AuthenticationService $authService;
    private AuthorizationService $authorizationService;
    private UserService $userService;


    public function __construct(AuthenticationService $authService, AuthorizationService $authorizationService, UserService $userService)
    {
        $this-> userService = $userService;
        $this->authService = $authService;
        $this->authorizationService = $authorizationService;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $user = $this->userService->verifyUserPassword($data['username'], $data['password']);
            // Tạo JWT
            $accesstoken = $this->authService->createToken($user, 'access');
            $refreshtoken = $this->authService->createToken($user, 'refresh');
            return new JsonResponse(['accesstoken' => $accesstoken, 'refreshtoken' => $refreshtoken]);
        } catch (\Exception $e) {
            return new JsonResponse($e, 400);
        }
    }

    #[Route('/api/protected', name: 'api_protected', methods: ['GET'])]
    public function protectedEndpoint(Request $request): JsonResponse
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse(['error' => 'Missing or invalid Authorization header'], 401);
        }

        $jwt = substr($authHeader, 7); // Lấy JWT từ Authorization header

        // Xác thực token và lấy thông tin người dùng
        $user = $this->authService->getUserFromToken($jwt);

        if (!$user) {
            return new JsonResponse(['error' => 'Invalid or expired token'], 401);
        }

        // Kiểm tra quyền
        if (!$this->authorizationService->checkPermissions($user, ['ROLE_ADMIN'])) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        return new JsonResponse(['message' => 'Access granted', 'user' => [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
        ]]);
    }

}
