<?php

namespace App\Service;

use App\Entity\User;
use App\Service\RefreshTokenService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RefreshTokenRepository;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint;
use InvalidArgumentException;
use DateTimeImmutable;

class AuthenticationService
{
    private Configuration $config;
    private string $issuer;
    private string $audience;
    private RefreshTokenRepository $refreshTokenRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        string $secretKey,
        string $issuer,
        string $audience,
        RefreshTokenRepository $refreshTokenRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->issuer = $issuer;
        $this->audience = $audience;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->entityManager = $entityManager;

        $this->config = Configuration::forSymmetricSigner(
            new \Lcobucci\JWT\Signer\Hmac\Sha256(),
            \Lcobucci\JWT\Signer\Key\InMemory::plainText($secretKey)
        );
    }

    /**
     * Tạo token JWT.
     */
    public function createToken(User $user, string $tokenType): string
    {
        $now = new \DateTimeImmutable();
        $ttl = match ($tokenType) {
            'access' => 3600,       // 1 giờ cho access token
            'refresh' => 5184000,   // 2 tháng cho refresh token
            default => throw new \InvalidArgumentException('Invalid token type. Allowed values are "access" and "refresh".')
        };

        // Tạo token
        $token = $this->config->builder()
            ->issuedBy($this->issuer)             // Claim `iss`
            ->permittedFor($this->audience)      // Claim `aud`
            ->identifiedBy(bin2hex(random_bytes(32)), true) // Claim `jti` với độ dài 64 ký tự
            ->issuedAt($now)                     // Claim `iat`
            ->expiresAt($now->modify("+$ttl seconds")) // Claim `exp`
            ->withClaim('uid', $user->getId())          // User ID
            ->withClaim('username', $user->getUsername()) // Username
            ->withClaim('email', $user->getEmail())      // Email
            ->withClaim('isActive', $user->isActive())   // Active status
            ->withClaim('type', $tokenType)             // Token type: `access` or `refresh`
            ->getToken($this->config->signer(), $this->config->signingKey());

        // Nếu là refresh token, lưu vào cơ sở dữ liệu
        if ($tokenType === 'refresh') {
            $jti = $token->claims()->get('jti'); // Lấy ID token từ claim `jti`
            $expTimestamp = $token->claims()->get('exp')->getTimestamp(); // Chuyển thành timestamp
        
            // Chuyển timestamp thành DateTime
            $expiresAt = (new \DateTime())->setTimestamp($expTimestamp);
        
            $refreshTokenService = new RefreshTokenService($this->refreshTokenRepository, $this->entityManager);
            $refreshTokenService->createToken($jti, $expiresAt);
        }

        return $token->toString();
    }



    /**
     * Giải mã và xác minh token JWT.
     */
    public function decodeToken(string $jwt): ?Plain
    {
        try {
            $token = $this->config->parser()->parse($jwt);

            if (!$token instanceof Plain) {
                throw new InvalidArgumentException('Invalid token format.');
            }

            // Thêm các ràng buộc kiểm tra
            $constraints = [
                new Constraint\IssuedBy($this->issuer),
                new Constraint\PermittedFor($this->audience),
                new Constraint\ValidAt(new DateTimeImmutable())
            ];

            $this->config->validator()->assert($token, ...$constraints);

            return $token;
        } catch (\Throwable $e) {
            // Ghi log lỗi nếu cần, để tiện debug
            // $this->logger->error('Token validation failed: ' . $e->getMessage());
            return null; // Trả về null nếu token không hợp lệ
        }
    }

    public function getUserFromToken(string $jwt): ?User
    {
        $token = $this->decodeToken($jwt);

        if (!$token) {
            return null; // Token không hợp lệ
        }

        // Lấy thông tin người dùng từ claim `uid`
        $userId = $token->claims()->get('uid');
        return $this->entityManager->getRepository(User::class)->find($userId);
    }

}
