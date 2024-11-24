<?php

namespace App\Service;

use Lcobucci\JWT\Token\Plain;

class AuthorizationService
{
    /**
     * Kiểm tra quyền dựa trên roles có trong JWT.
     *
     * @param Plain $token JWT đã giải mã.
     * @param array $requiredRoles Danh sách quyền yêu cầu.
     * @return bool
     */
    public function checkPermissions(Plain $token, array $requiredRoles): bool
    {
        try {
            // Lấy danh sách roles từ claims của token
            $roles = $token->claims()->get('roles', []);

            // Kiểm tra nếu có ít nhất một quyền trùng khớp
            return !empty(array_intersect($requiredRoles, $roles));
        } catch (\Throwable $e) {
            return false;
        }
    }
}
