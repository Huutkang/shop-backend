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
use App\Exception\AppException;



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
    
        // Thêm các ràng buộc xác thực vào config
        $this->config->setValidationConstraints(
            new \Lcobucci\JWT\Validation\Constraint\IssuedBy($this->issuer),
            new \Lcobucci\JWT\Validation\Constraint\PermittedFor($this->audience),
            new \Lcobucci\JWT\Validation\Constraint\SignedWith(
                $this->config->signer(),
                $this->config->signingKey()
            )
        );
    }
    

    /**
     * Tạo token JWT.
     */
    public function createToken(User $user, string $tokenType): string
    {
        $now = new DateTimeImmutable();
        $ttl = match ($tokenType) {
            'access' => 3600,       // 1 giờ cho access token
            'refresh' => 5184000,   // 2 tháng cho refresh token
            default => throw new InvalidArgumentException('Invalid token type. Allowed values are "access" and "refresh".')
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
     * Xác thực JWT token.
     */
    public function validateToken(string $token): Plain
    {
        $token = $this->config->parser()->parse($token);

        if (!$token instanceof Plain) {
            throw new AppException('E1020');
        }

        // Lấy các ràng buộc từ cấu hình
        $constraints = $this->config->validationConstraints();

        // Xác minh token với các ràng buộc
        $this->config->validator()->assert($token, ...$constraints);

        // Kiểm tra thời gian
        $now = new DateTimeImmutable();
        if ($token->isExpired($now)) {
            throw new AppException('E1021');
        }

        return $token;
    }


}