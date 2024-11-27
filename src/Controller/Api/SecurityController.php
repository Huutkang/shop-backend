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

    #[Route('/api/cp', name: 'api_cp', methods: ['GET'])]
    public function checkPermission(Request $request): JsonResponse
    {
        $user = $request->attributes->get('user');
        if ($user) {
            $this->authorizationService->checkPermission($user, "view_users");
            return new JsonResponse(['message' => 'Đã cấp quyền'], 200);
        } else {
            throw new AppException("E2002");
        }
        
        
    }

}
