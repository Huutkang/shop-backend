
# Web bán hàng (Symfony Backend)

Đây là phần backend của dự án web bán hàng, được phát triển bằng Symfony. Dự án gồm 2 phần chính:

- **Frontend**: React, lưu trữ tại [Shop Frontend Repository](https://github.com/Huutkang/shop-frontend.git)
- **Backend**: Symfony, lưu trữ tại [Shop Backend Repository](https://github.com/Huutkang/shop-backend.git)

---

## Yêu cầu hệ thống

- **PHP**: Phiên bản `8.2` hoặc mới hơn.
- **Composer**: Được cài đặt sẵn trên hệ thống.
- **Database**: MySQL hoặc PostgreSQL.
- **Web Server**: Apache hoặc Nginx.
- **Symfony CLI**: Khuyến nghị cài đặt để hỗ trợ phát triển.

---

## Hướng dẫn cài đặt

1. **Clone dự án từ GitHub**:
   ```bash
   git clone https://github.com/Huutkang/shop-backend.git
   cd shop-backend
   ```

2. **Cài đặt các thư viện PHP bằng Composer**:
   ```bash
   composer install
   ```

3. **Thiết lập file cấu hình `.env`**:
   - Cấu hình kết nối database trong file `.env`:
     ```env
     DATABASE_URL="mysql://username:passwork@127.0.0.1:3306/shop?serverVersion=mariadb-10.4.32"
     ```

4. **Khởi tạo cơ sở dữ liệu**:
   - Tạo database:
     ```bash
     php bin/console doctrine:database:create
     ```
   - Chạy migrations:
     ```bash
     php bin/console doctrine:migrations:migrate
     ```

5. **Chạy server phát triển**:
   - Sử dụng Symfony CLI hoặc PHP built-in server:
     ```bash
     symfony server:start
     ```
   - Hoặc:
     ```bash
     php -S 127.0.0.1:8000 -t public
     ```
   - Truy cập ứng dụng tại [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## Quy tắc làm việc nhóm

1. **Làm việc trên nhánh cá nhân**:
   - Mỗi thành viên làm việc trên nhánh của mình (`an`, `thang`, `phuc`, `trung`).
   - **Không đẩy trực tiếp lên nhánh `main`**.

2. **Pull mã nguồn trước khi làm việc**:
   - Để đảm bảo mã nguồn của bạn luôn cập nhật:
     ```bash
     git pull origin <tên_nhánh>
     ```

3. **Commit và đẩy lên nhánh cá nhân**:
   - Sau khi hoàn thành công việc, hãy commit và đẩy mã lên nhánh của bạn:
     ```bash
     git add .
     git commit -m "Mô tả thay đổi"
     git push origin <tên_nhánh>
     ```

4. **Yêu cầu đẩy lên nhánh `main`**:
   - Khi cảm thấy công việc của bạn đã hoàn thiện và ổn định, gửi yêu cầu (Pull Request) để được xem xét và hợp nhất vào nhánh `main`.

---

## Một số lưu ý

1. **Cài đặt thư viện mới**:
   - Nếu cần cài đặt thêm thư viện PHP, hãy sử dụng:
     ```bash
     composer require <tên_thư_viện>
     ```
   - Commit file `composer.json` và `composer.lock`.

2. **Liên hệ**:
   - Nếu gặp vấn đề, liên hệ **Nguyễn Hữu Thắng** hoặc các thành viên khác để được hỗ trợ.

---
