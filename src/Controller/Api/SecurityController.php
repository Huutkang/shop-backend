<?php

namespace App\Controller\Api;

use App\Service\UserService;
use App\Service\AuthenticationService;
use App\Service\AuthorizationService;
use App\Service\GroupMemberService;
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
    private GroupMemberService $groupMemberService;

    public function __construct(AuthenticationService $authService, AuthorizationService $authorizationService, UserService $userService, GroupMemberService $groupMemberService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
        $this->authorizationService = $authorizationService;
        $this->groupMemberService = $groupMemberService;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $user = $this->userService->verifyUserPassword($data['username'], $data['password']);
            // Tạo JWT
            $accessToken = $this->authService->createToken($user, 'access');
            $refreshToken = $this->authService->createToken($user, 'refresh');
            return new JsonResponse(['accessToken' => $accessToken, 'refreshToken' => $refreshToken]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/refresh-token', name: 'api_refresh_token', methods: ['POST'])]
    public function refreshToken(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $refreshToken = $data['refreshToken'] ?? null;

            if (!$refreshToken) {
                throw new AppException('E1025', 'Refresh token is required.');
            }

            // Cấp lại Access Token từ Refresh Token
            $accessToken = $this->authService->refreshAccessToken($refreshToken);

            return new JsonResponse(['accessToken' => $accessToken]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        // Xử lý logout, ví dụ: xóa token ở phía client hoặc cập nhật trạng thái token
        // Trong trường hợp này, không làm gì thêm nếu đã có refresh token kiểm tra.

        return new JsonResponse(['message' => 'Logout successful'], 200);
    }

    #[Route('/api/change-password', name: 'api_change_password', methods: ['POST'])]
    public function changePassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->getUser();  // Lấy thông tin người dùng từ session hoặc token

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $currentPassword = $data['currentPassword'] ?? null;
        $newPassword = $data['newPassword'] ?? null;

        if (!$currentPassword || !$newPassword) {
            return new JsonResponse(['error' => 'Both current and new password are required.'], 400);
        }

        try {
            // Gọi service để xác thực mật khẩu hiện tại và thay đổi mật khẩu mới
            $this->userService->changeUserPassword($user, $currentPassword, $newPassword);
            return new JsonResponse(['message' => 'Password changed successfully.'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/verify-password', name: 'api_verify_password', methods: ['POST'])]
    public function verifyPassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->getUser();  // Lấy thông tin người dùng từ session hoặc token

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $password = $data['password'] ?? null;

        if (!$password) {
            return new JsonResponse(['error' => 'Password is required.'], 400);
        }

        try {
            // Gọi service để xác thực lại mật khẩu
            $isValid = $this->userService->verifyPassword($user, $password);
            
            if ($isValid) {
                return new JsonResponse(['message' => 'Password is correct.'], 200);
            } else {
                return new JsonResponse(['error' => 'Incorrect password.'], 400);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/cp', name: 'api_cp', methods: ['GET'])]
    public function checkPermission(Request $request): JsonResponse
    {
        $user = $request->attributes->get('user');
        if ($user) {
            $a = $this->authorizationService->checkPermission($user, "manage_system_settings");
            $b = $this->groupMemberService->getGroupsByUser($user);
            if ($a) {
                return new JsonResponse(['message' => 'Đã cấp quyền', 'nhóm' => $b], 200);
            } else {
                return new JsonResponse(['message' => 'Chưa cấp quyền'], 200);
            }
        } else {
            throw new AppException("E2002");
        }
    }
}
