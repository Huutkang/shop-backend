<?php

namespace App\Service;

use App\Entity\User;



class AuthorizationService
{
    private $userPermissionService;
    private $groupMemberService;
    private $groupPermissionService;

    public function __construct(
        UserPermissionService $userPermissionService,
        GroupMemberService $groupMemberService,
        GroupPermissionService $groupPermissionService
    ) {
        $this->userPermissionService = $userPermissionService;
        $this->groupMemberService = $groupMemberService;
        $this->groupPermissionService = $groupPermissionService;
    }

    /**
     * Kiểm tra quyền của người dùng hoặc nhóm.
     *
     * @param User $user Người dùng cần kiểm tra
     * @param string $permissionName Tên quyền cần kiểm tra
     * @param int|null $targetId Đối tượng đích cần kiểm tra
     * @return bool Trả về true nếu người dùng hoặc nhóm có quyền, false nếu không
     */
    public function checkPermission(User $user, string $permissionName, ?int $targetId = null): bool
    {
        // 1. Kiểm tra quyền của người dùng
        if ($this->userPermissionService->hasPermission($user->getId(), $permissionName, $targetId)) {
            return true;
        }

        // 2. Lấy danh sách các nhóm mà người dùng thuộc về
        $groups = $this->groupMemberService->getGroupsByUser($user);

        // 3. Kiểm tra quyền của từng nhóm
        foreach ($groups as $group) {
            if ($this->groupPermissionService->hasPermission($group, $permissionName, $targetId)) {
                return true;
            }
        }

        // 4. Không tìm thấy quyền hợp lệ
        return false;
    }
}


// 1.  Quản lý người dùng
//     Xem danh sách người dùng (view_users)
//     Xem chi tiết người dùng (view_user_details)
//     Tạo người dùng mới (create_user)
//     Chỉnh sửa thông tin người dùng (edit_user)
//     Xóa người dùng (delete_user)
//     Kích hoạt/khóa người dùng (activate_deactivate_user)
//     Quản lý phân quyền cá nhân (manage_user_permissions)

// 2.  Quản lý nhóm
//     Xem danh sách nhóm (view_groups)
//     Xem chi tiết nhóm (view_group_details)
//     Tạo nhóm mới (create_group)
//     Chỉnh sửa thông tin nhóm (edit_group)
//     Xóa nhóm (delete_group)
//     Quản lý thành viên nhóm (manage_group_members)
//     Quản lý phân quyền nhóm (manage_group_permissions)

// 3.  Quản lý quyền (Permissions)
//     Xem danh sách quyền (view_permissions)
//     Tạo quyền mới (create_permission)
//     Chỉnh sửa quyền (edit_permission)
//     Xóa quyền (delete_permission)

// 4.  Quản lý sản phẩm
//     Xem danh sách sản phẩm (view_products)
//     Xem chi tiết sản phẩm (view_product_details)
//     Tạo sản phẩm mới (create_product)
//     Chỉnh sửa thông tin sản phẩm (edit_product)
//     Xóa sản phẩm (delete_product)
//     Quản lý sản phẩm nổi bật (manage_featured_products)
//     Quản lý số lượng tồn kho (manage_product_stock)

// 5.  Quản lý danh mục (Categories)
//     Xem danh sách danh mục (view_categories)
//     Tạo danh mục mới (create_category)
//     Chỉnh sửa danh mục (edit_category)
//     Xóa danh mục (delete_category)

// 6.  Quản lý giỏ hàng
//     Xem giỏ hàng của người dùng (view_carts)
//     Chỉnh sửa giỏ hàng của người dùng (edit_carts)
//     Xóa giỏ hàng của người dùng (delete_carts)

// 7.  Quản lý danh sách yêu thích (Wishlist)
//     Xem danh sách yêu thích của người dùng (view_wishlists)
//     Chỉnh sửa danh sách yêu thích của người dùng (edit_wishlists)
//     Xóa sản phẩm khỏi danh sách yêu thích (delete_wishlists)

// 8.  Quản lý mã giảm giá (Coupons)
//     Xem danh sách mã giảm giá (view_coupons)
//     Tạo mã giảm giá mới (create_coupon)
//     Chỉnh sửa mã giảm giá (edit_coupon)
//     Xóa mã giảm giá (delete_coupon)
//     Kích hoạt/Vô hiệu hóa mã giảm giá (activate_deactivate_coupon)

// 9.  Quản lý đơn hàng
//     Xem danh sách đơn hàng (view_orders)
//     Xem chi tiết đơn hàng (view_order_details)
//     Cập nhật trạng thái vận chuyển (update_shipping_status)
//     Cập nhật trạng thái thanh toán (update_payment_status)
//     Xóa đơn hàng (delete_order)

// 10. Quản lý đánh giá sản phẩm (Reviews)
//     Xem danh sách đánh giá (view_reviews)
//     Duyệt/Không duyệt đánh giá (approve_disapprove_review)
//     Xóa đánh giá (delete_review)

// 11. Quản lý toàn hệ thống
//     Truy cập Dashboard quản trị (access_admin_dashboard)
//     Quản lý cấu hình hệ thống (manage_system_settings)
//     Quản lý nhật ký hệ thống (view_system_logs)

// mỗi người dùng được xem các dữ liệu của mình: giỏ hàng, đánh giá, thanh toán. cần có hàm riêng để cho chính người dùng ấy xem.

// các quyền được ghi trong từng hàm của controller. hàm hasPermissions nhận vào id người dùng, quyền trong hàm cần kiểm tra
// hàm này xác định tập quyền của người dùng, nếu người dùng có quyền thì trả về true, nếu không trả về false.

