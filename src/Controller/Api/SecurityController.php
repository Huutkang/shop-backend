<?php

namespace App\Controller\Api;

use App\Service\AuthenticationService;
use App\Service\AuthorizationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    private AuthenticationService $authService;
    private AuthorizationService $authorizationService;

    public function __construct(AuthenticationService $authService, AuthorizationService $authorizationService)
    {
        $this->authService = $authService;
        $this->authorizationService = $authorizationService;
    }

    /**
     * @Route("/api/login", methods={"POST"})
     */
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Kiểm tra username và password
        if ($data['username'] === 'admin' && $data['password'] === 'password') {
            // Tạo JWT
            $token = $this->authService->createToken(1, ['ROLE_ADMIN']);
            return new JsonResponse(['token' => $token]);
        }

        return new JsonResponse(['error' => 'Invalid credentials'], 401);
    }

    /**
     * @Route("/api/protected", methods={"GET"})
     */
    public function protectedEndpoint(Request $request): JsonResponse
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse(['error' => 'Missing or invalid Authorization header'], 401);
        }

        $jwt = substr($authHeader, 7); // Lấy JWT từ Authorization header

        // Xác thực token
        $token = $this->authService->decodeToken($jwt);

        if (!$token) {
            return new JsonResponse(['error' => 'Invalid token'], 401);
        }

        // Kiểm tra quyền
        if (!$this->authorizationService->checkPermissions($token, ['ROLE_ADMIN'])) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        return new JsonResponse(['message' => 'Access granted']);
    }
}
