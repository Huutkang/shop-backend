<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthenticationService
{
    private $userRepository;
    private $secretKey = 'your_secret_key';
    private $accessTokenTTL = 3600; // 1 giờ
    private $refreshTokenTTL = 604800; // 7 ngày

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(string $username, string $password): array
    {
        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (!$user || !password_verify($password, $user->getPassword())) {
            throw new \Exception("Thông tin đăng nhập không hợp lệ");
        }

        $accessToken = $this->generateToken($user, $this->accessTokenTTL);
        $refreshToken = $this->generateToken($user, $this->refreshTokenTTL, true);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }

    public function logout(string $token): void
    {
        // Thêm token vào blacklist
        // Lưu token vào bảng `blacklist_tokens` với thời hạn hết hạn của token
    }

    public function refreshToken(string $refreshToken): string
    {
        try {
            $decoded = JWT::decode($refreshToken, new Key($this->secretKey, 'HS256'));
            $user = $this->userRepository->find($decoded->sub);

            if (!$user) {
                throw new \Exception("Token không hợp lệ");
            }

            return $this->generateToken($user, $this->accessTokenTTL);
        } catch (\Exception $e) {
            throw new \Exception("Token không hợp lệ hoặc đã hết hạn");
        }
    }

    private function generateToken(User $user, int $ttl, bool $isRefresh = false): string
    {
        $payload = [
            'sub' => $user->getId(),
            'exp' => time() + $ttl,
            'type' => $isRefresh ? 'refresh' : 'access',
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
}
