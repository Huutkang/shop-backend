<?php 


namespace App\Controller;

use App\Service\AuthenticationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController
{
    private $authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        try {
            $tokens = $this->authService->login($username, $password);
            return new JsonResponse($tokens, 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $token);

        try {
            $this->authService->logout($token);
            return new JsonResponse(['message' => 'Đăng xuất thành công'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $refreshToken = $data['refresh_token'] ?? '';

        try {
            $newToken = $this->authService->refreshToken($refreshToken);
            return new JsonResponse(['access_token' => $newToken], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 401);
        }
    }
}
