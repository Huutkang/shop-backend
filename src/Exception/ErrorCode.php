<?php

namespace App\Exception;


class ErrorCode {
    // 1.1. Lỗi chung (0000–0999)
    const E0000 = ['code' => 0, 'message' => 'Lỗi không xác định', 'httpStatus' => 500];
    const E0001 = ['code' => 1, 'message' => 'Yêu cầu không hợp lệ', 'httpStatus' => 400];
    const E0002 = ['code' => 2, 'message' => 'Hệ thống quá tải', 'httpStatus' => 503];
    const E0003 = ['code' => 3, 'message' => 'Hết thời gian chờ', 'httpStatus' => 408];
    const E0004 = ['code' => 4, 'message' => 'Lỗi mạng', 'httpStatus' => 503];
    const E0005 = ['code' => 5, 'message' => 'Dịch vụ không khả dụng', 'httpStatus' => 503];

    // 1.2. User Service (1000–1999)
    const E1000 = ['code' => 1000, 'message' => 'Người dùng không tồn tại', 'httpStatus' => 404];
    const E1001 = ['code' => 1001, 'message' => 'Email không hợp lệ', 'httpStatus' => 400];
    const E1002 = ['code' => 1002, 'message' => 'Mật khẩu sai', 'httpStatus' => 401];
    const E1003 = ['code' => 1003, 'message' => 'Tài khoản bị khóa', 'httpStatus' => 403];
    const E1004 = ['code' => 1004, 'message' => 'Tài khoản đã tồn tại', 'httpStatus' => 409];

    // 1.3. Authentication/Authorization Service (2000–2999)
    const E2000 = ['code' => 2000, 'message' => 'Token không hợp lệ', 'httpStatus' => 401];
    const E2001 = ['code' => 2001, 'message' => 'Token đã hết hạn', 'httpStatus' => 401];
    const E2002 = ['code' => 2002, 'message' => 'Không có quyền truy cập', 'httpStatus' => 403];
    const E2003 = ['code' => 2003, 'message' => 'Thiếu thông tin xác thực', 'httpStatus' => 400];
    const E2004 = ['code' => 2004, 'message' => 'Lỗi xác thực liên dịch vụ', 'httpStatus' => 502];

    // 1.4. Payment Service (3000–3999)
    const E3000 = ['code' => 3000, 'message' => 'Thanh toán thất bại', 'httpStatus' => 400];
    const E3001 = ['code' => 3001, 'message' => 'Thẻ không hợp lệ', 'httpStatus' => 400];
    const E3002 = ['code' => 3002, 'message' => 'Không đủ số dư', 'httpStatus' => 402];
    const E3003 = ['code' => 3003, 'message' => 'Phương thức thanh toán không được hỗ trợ', 'httpStatus' => 400];
    const E3004 = ['code' => 3004, 'message' => 'Giao dịch bị nghi ngờ gian lận', 'httpStatus' => 403];

    // 1.5. Notification Service (4000–4999)
    const E4000 = ['code' => 4000, 'message' => 'Gửi thông báo thất bại', 'httpStatus' => 500];
    const E4001 = ['code' => 4001, 'message' => 'Kênh thông báo không được hỗ trợ', 'httpStatus' => 400];
    const E4002 = ['code' => 4002, 'message' => 'Lỗi định dạng thông báo', 'httpStatus' => 400];
    const E4003 = ['code' => 4003, 'message' => 'Người nhận không hợp lệ', 'httpStatus' => 400];

    // 1.6. Data Service (5000–5999)
    const E5000 = ['code' => 5000, 'message' => 'Không tìm thấy dữ liệu', 'httpStatus' => 404];
    const E5001 = ['code' => 5001, 'message' => 'Lỗi tải lên dữ liệu', 'httpStatus' => 500];
    const E5002 = ['code' => 5002, 'message' => 'Lỗi định dạng dữ liệu', 'httpStatus' => 400];
    const E5003 = ['code' => 5003, 'message' => 'Hết dung lượng lưu trữ', 'httpStatus' => 507];
    const E5004 = ['code' => 5004, 'message' => 'Lỗi kết nối đến hệ thống lưu trữ', 'httpStatus' => 500];

    // 2.1. Web bán hàng (10000–19999)
    const E10000 = ['code' => 10000, 'message' => 'Người dùng chưa đăng nhập', 'httpStatus' => 401];
    const E10200 = ['code' => 10200, 'message' => 'Sản phẩm không tồn tại', 'httpStatus' => 404];
    const E10201 = ['code' => 10201, 'message' => 'Sản phẩm hết hàng', 'httpStatus' => 409];
    const E10400 = ['code' => 10400, 'message' => 'Thanh toán thất bại', 'httpStatus' => 400];

    // 2.2. Web lưu trữ dữ liệu (20000–29999)
    const E20000 = ['code' => 20000, 'message' => 'File vượt quá kích thước cho phép', 'httpStatus' => 413];
    const E20200 = ['code' => 20200, 'message' => 'File không tồn tại', 'httpStatus' => 404];

    // 2.3. Mạng xã hội (30000–39999)
    const E30000 = ['code' => 30000, 'message' => 'Người dùng bị cấm', 'httpStatus' => 403];
    const E30200 = ['code' => 30200, 'message' => 'Bài viết không tồn tại', 'httpStatus' => 404];

    // 2.4. Ứng dụng khác (40000–49999)
    const E40000 = ['code' => 40000, 'message' => 'Lỗi không xác định trong ứng dụng', 'httpStatus' => 500];

    
}
