<?php

namespace App\Service;

use App\Entity\Permission;
use Doctrine\ORM\EntityManagerInterface;

class PermissionService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Đồng bộ danh sách quyền giữa cơ sở dữ liệu và danh sách quyền định nghĩa sẵn
     */
    public function syncPermissions(): void
    {
        // Danh sách quyền tĩnh định nghĩa
        $permissions = [
            // Quản lý người dùng
            'view_users' => 'Xem danh sách người dùng',
            'view_user_details' => 'Xem chi tiết người dùng',
            'create_user' => 'Tạo người dùng mới',
            'edit_user' => 'Chỉnh sửa thông tin người dùng',
            'delete_user' => 'Xóa người dùng',
            'activate_deactivate_user' => 'Kích hoạt/khóa người dùng',
            'manage_user_permissions' => 'Quản lý phân quyền cá nhân',

            // Quản lý nhóm
            'view_groups' => 'Xem danh sách nhóm',
            'view_group_details' => 'Xem chi tiết nhóm',
            'create_group' => 'Tạo nhóm mới',
            'edit_group' => 'Chỉnh sửa thông tin nhóm',
            'delete_group' => 'Xóa nhóm',
            'manage_group_members' => 'Quản lý thành viên nhóm',
            'manage_group_permissions' => 'Quản lý phân quyền nhóm',

            // Quản lý quyền
            'view_permissions' => 'Xem danh sách quyền',
            'create_permission' => 'Tạo quyền mới',
            'edit_permission' => 'Chỉnh sửa quyền',
            'delete_permission' => 'Xóa quyền',

            // Quản lý sản phẩm
            'view_products' => 'Xem danh sách sản phẩm',
            'view_product_details' => 'Xem chi tiết sản phẩm',
            'create_product' => 'Tạo sản phẩm mới',
            'edit_product' => 'Chỉnh sửa thông tin sản phẩm',
            'delete_product' => 'Xóa sản phẩm',
            'manage_featured_products' => 'Quản lý sản phẩm nổi bật',
            'manage_product_stock' => 'Quản lý số lượng tồn kho',

            // Quản lý danh mục
            'view_categories' => 'Xem danh sách danh mục',
            'create_category' => 'Tạo danh mục mới',
            'edit_category' => 'Chỉnh sửa danh mục',
            'delete_category' => 'Xóa danh mục',

            // Quản lý giỏ hàng
            'view_carts' => 'Xem giỏ hàng của người dùng',
            'edit_carts' => 'Chỉnh sửa giỏ hàng của người dùng',
            'delete_carts' => 'Xóa giỏ hàng của người dùng',

            // Quản lý danh sách yêu thích
            'view_wishlists' => 'Xem danh sách yêu thích của người dùng',
            'edit_wishlists' => 'Chỉnh sửa danh sách yêu thích của người dùng',
            'delete_wishlists' => 'Xóa sản phẩm khỏi danh sách yêu thích',

            // Quản lý mã giảm giá
            'view_coupons' => 'Xem danh sách mã giảm giá',
            'create_coupon' => 'Tạo mã giảm giá mới',
            'edit_coupon' => 'Chỉnh sửa mã giảm giá',
            'delete_coupon' => 'Xóa mã giảm giá',
            'activate_deactivate_coupon' => 'Kích hoạt/Vô hiệu hóa mã giảm giá',

            // Quản lý đơn hàng
            'view_orders' => 'Xem danh sách đơn hàng',
            'view_order_details' => 'Xem chi tiết đơn hàng',
            'update_shipping_status' => 'Cập nhật trạng thái vận chuyển',
            'update_payment_status' => 'Cập nhật trạng thái thanh toán',
            'delete_order' => 'Xóa đơn hàng',

            // Quản lý đánh giá sản phẩm
            'view_reviews' => 'Xem danh sách đánh giá',
            'approve_disapprove_review' => 'Duyệt/Không duyệt đánh giá',
            'delete_review' => 'Xóa đánh giá',

            // Quản lý toàn hệ thống
            'access_admin_dashboard' => 'Truy cập Dashboard quản trị',
            'manage_system_settings' => 'Quản lý cấu hình hệ thống',
            'view_system_logs' => 'Quản lý nhật ký hệ thống',
        ];

        $repository = $this->entityManager->getRepository(Permission::class);

        // Lấy danh sách quyền hiện có trong cơ sở dữ liệu
        $existingPermissions = $repository->findAll();
        $existingNames = array_map(fn($permission) => $permission->getName(), $existingPermissions);

        // Thêm các quyền mới chưa có trong cơ sở dữ liệu
        foreach ($permissions as $name => $description) {
            if (!in_array($name, $existingNames)) {
                $permission = new Permission();
                $permission->setName($name)
                           ->setDescription($description);
                $this->entityManager->persist($permission);
            }
        }

        // Xóa các quyền thừa không có trong danh sách tĩnh
        foreach ($existingPermissions as $permission) {
            if (!array_key_exists($permission->getName(), $permissions)) {
                $this->entityManager->remove($permission);
            }
        }

        // Ghi lại các thay đổi vào cơ sở dữ liệu
        $this->entityManager->flush();
    }
}
