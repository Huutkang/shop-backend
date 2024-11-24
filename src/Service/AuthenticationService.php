<?php

namespace App\Service;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint;

class AuthenticationService
{
    private Configuration $config;
    private string $issuer;
    private string $audience;

    public function __construct(string $secretKey, string $issuer, string $audience)
    {
        $this->issuer = $issuer;
        $this->audience = $audience;

        $this->config = Configuration::forSymmetricSigner(
            new \Lcobucci\JWT\Signer\Hmac\Sha256(),
            \Lcobucci\JWT\Signer\Key\InMemory::plainText($secretKey)
        );
    }

    /**
     * Tạo token JWT.
     */
    public function createToken(int $userId, array $roles = [], int $ttl = 3600): string
    {
        $now = new \DateTimeImmutable();

        $token = $this->config->builder()
            ->issuedBy($this->issuer)             // Claim `iss`
            ->permittedFor($this->audience)      // Claim `aud`
            ->identifiedBy(uniqid(), true)       // Claim `jti`
            ->issuedAt($now)                     // Claim `iat`
            ->expiresAt($now->modify("+$ttl seconds")) // Claim `exp`
            ->withClaim('uid', $userId)          // Claim tùy chỉnh `uid`
            ->withClaim('roles', $roles)         // Claim tùy chỉnh `roles`
            ->getToken($this->config->signer(), $this->config->signingKey());

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
                throw new \InvalidArgumentException('Invalid token format.');
            }

            // Thêm các ràng buộc kiểm tra
            $constraints = [
                new Constraint\IssuedBy($this->issuer),
                new Constraint\PermittedFor($this->audience),
                new Constraint\ValidAt(new \DateTimeImmutable())
            ];

            $this->config->validator()->assert($token, ...$constraints);

            return $token;
        } catch (\Throwable $e) {
            return null; // Trả về null nếu token không hợp lệ
        }
    }
}
