<?php

namespace App\Service;

use App\Entity\User;
use App\Service\BlacklistTokenService;
use App\Service\RefreshTokenService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RefreshTokenRepository;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token\Plain;
use InvalidArgumentException;
use DateTimeImmutable;
use App\Exception\AppException;



class AuthenticationService
{
    private Configuration $config;
    private string $issuer;
    private string $audience;
    private UserService $userService;
    private RefreshTokenRepository $refreshTokenRepository;
    private EntityManagerInterface $entityManager;
    private BlacklistTokenService $blacklistTokenService;
    private RefreshTokenService $refreshTokenService;

    public function __construct(
        string $secretKey,
        UserService $userService,
        RefreshTokenRepository $refreshTokenRepository,
        EntityManagerInterface $entityManager,
        BlacklistTokenService $blacklistTokenService,
        RefreshTokenService $refreshTokenService
    ) {
        // Lấy giá trị từ file .env
        $this->issuer = $_ENV['JWT_ISSUER'] ?? 'https://scime.click';
        $this->audience = $_ENV['JWT_AUDIENCE'] ?? 'https://shop.scime.click';

        $this->userService = $userService;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->entityManager = $entityManager;
        $this->blacklistTokenService = $blacklistTokenService;
        $this->refreshTokenService = $refreshTokenService;

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
     *
     * @param User $user Thông tin người dùng
     * @param string $tokenType Loại token (`access` hoặc `refresh`)
     * @param string|null $refreshTokenId ID của Refresh Token (bắt buộc nếu $tokenType là `access`)
     * @return string
     */
    public function createToken(User $user, string $tokenType, ?string $refreshTokenId = null): string
    {
        $now = new DateTimeImmutable();
        $ttl = match ($tokenType) {
            'access' => 3600,       // 1 giờ cho access token
            'refresh' => 5184000,   // 2 tháng cho refresh token
            default => throw new InvalidArgumentException('Invalid token type. Allowed values are "access" and "refresh".')
        };

        // Nếu là access token, refreshTokenId phải được cung cấp
        if ($tokenType === 'access' && !$refreshTokenId) {
            throw new InvalidArgumentException('Refresh Token ID is required for access tokens.');
        }

        // Tạo token
        $builder = $this->config->builder()
            ->issuedBy($this->issuer)             // Claim `iss`
            ->permittedFor($this->audience)      // Claim `aud`
            ->identifiedBy(bin2hex(random_bytes(32)), true) // Claim `jti` với độ dài 64 ký tự
            ->issuedAt($now)                     // Claim `iat`
            ->expiresAt($now->modify("+$ttl seconds")) // Claim `exp`
            ->withClaim('uid', $user->getId())          // User ID
            ->withClaim('username', $user->getUsername()) // Username
            ->withClaim('email', $user->getEmail())      // Email
            ->withClaim('isActive', $user->isActive())   // Active status
            ->withClaim('type', $tokenType);            // Token type: `access` or `refresh`

        // Nếu là access token, thêm thông tin về refresh token ID
        if ($tokenType === 'access') {
            $builder->withClaim('refreshId', $refreshTokenId);
        }

        $token = $builder->getToken($this->config->signer(), $this->config->signingKey());

        // Nếu là refresh token, lưu vào cơ sở dữ liệu
        if ($tokenType === 'refresh') {
            $jti = $token->claims()->get('jti'); // Lấy ID token từ claim `jti`
            $expTimestamp = $token->claims()->get('exp')->getTimestamp(); // Chuyển thành timestamp

            // Chuyển timestamp thành DateTime
            $expiresAt = (new \DateTime())->setTimestamp($expTimestamp);

            $this->refreshTokenService->createToken($jti, $expiresAt);
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

    /**
     * Cấp lại Access Token từ Refresh Token.
     *
     * @param string $refreshTokenString
     * @return string
     * @throws AppException
     */
    public function refreshAccessToken(string $refreshTokenString): string
    {
        // Phân tích và xác thực Refresh Token
        $refreshToken = $this->validateToken($refreshTokenString);

        // Lấy thông tin từ claims của Refresh Token
        $jti = $refreshToken->claims()->get('jti');
        $tokenType = $refreshToken->claims()->get('type');
        $userId = $refreshToken->claims()->get('uid');

        if ($tokenType !== 'refresh') {
            throw new AppException('E2050');
        }

        // Kiểm tra Refresh Token trong cơ sở dữ liệu thông qua RefreshTokenService
        $storedToken = $this->refreshTokenService->getTokenById($jti);

        if (!$storedToken) {
            throw new AppException('E2050'); // Refresh token không hợp lệ (k tồn tại trong db)
        }

        if ($storedToken->getExpiresAt() < new DateTimeImmutable()) {
            throw new AppException('E2051'); // Refresh token đã hết hạn
        }

        $user = $this->userService->getUserById($userId);

        // Tạo Access Token mới
        return $this->createToken($user, 'access');
    }



    /**
     * Đăng xuất người dùng bằng cách vô hiệu hóa Access Token và Refresh Token.
     *
     * @param string $accessTokenString Chuỗi Access Token
     * @throws AppException
     */
    public function logout(string $accessTokenString): void
    {
        // Xác thực và phân tích Access Token
        $accessToken = $this->validateToken($accessTokenString);

        // Lấy thông tin từ Access Token
        $jti = $accessToken->claims()->get('jti');             // ID của Access Token
        $expiresAt = $accessToken->claims()->get('exp')->getTimestamp(); // Thời gian hết hạn của Access Token
        $refreshId = $accessToken->claims()->get('refreshId'); // ID của Refresh Token

        if (!$refreshId) {
            throw new AppException('E2050', 'Refresh Token ID is missing in the Access Token.');
        }

        // Thêm Access Token vào Blacklist
        $this->blacklistTokenService->addToken($jti, (new \DateTime())->setTimestamp($expiresAt));

        // Xóa Refresh Token khỏi cơ sở dữ liệu
        $this->refreshTokenService->deleteToken($refreshId);
    }

}