
# Web Bán Hàng (Symfony Backend)

Đây là phần backend của dự án web bán hàng, được phát triển bằng Symfony. Dự án bao gồm 2 phần chính:

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

### 1. Clone dự án từ GitHub
```bash
git clone https://github.com/Huutkang/shop-backend.git
cd shop-backend
```

### 2. Cài đặt các thư viện PHP
```bash
composer install
```

### 3. Thiết lập cấu hình kết nối cơ sở dữ liệu
- Mở file `.env` trong thư mục dự án và chỉnh sửa dòng `DATABASE_URL` sao cho phù hợp với thông tin cơ sở dữ liệu của bạn. Ví dụ:
  ```env
  DATABASE_URL="postgresql://username:password@127.0.0.1:5432/shop?serverVersion=17.2"
  ```
  Hoặc:
  ```env
  DATABASE_URL="mysql://root:root@127.0.0.1:3306/shop?serverVersion=mariadb-10.4.32"
  ```

### 4. Khởi tạo cơ sở dữ liệu
- Tạo database:
  ```bash
  php bin/console doctrine:database:create
  ```
- Tạo file migration:
  ```bash
  php bin/console doctrine:migrations:diff
  ```
- Thực thi migrations:
  ```bash
  php bin/console doctrine:migrations:migrate
  ```

#### Nếu cần xóa và tạo lại cơ sở dữ liệu
- Xóa database hiện tại:
  ```bash
  php bin/console doctrine:database:drop --force
  ```
- Xóa các file trong thư mục `migrations` nếu cần.
- Lặp lại các bước khởi tạo cơ sở dữ liệu ở trên.

### 5. Thiết lập dữ liệu ban đầu
- Chạy lệnh để tạo mật khẩu cho tài khoản **superadmin** và cập nhật quyền:
  ```bash
  php bin/console app:setup-initial
  ```

### 6. Chạy server phát triển
- Sử dụng Symfony CLI:
  ```bash
  symfony server:start
  ```
- Hoặc PHP built-in server:
  ```bash
  php -S 127.0.0.1:8000 -t public
  ```
- Truy cập ứng dụng tại [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## Quy tắc làm việc nhóm

1. **Làm việc trên nhánh cá nhân**:
   - Mỗi thành viên làm việc trên nhánh của mình (`an`, `thang`, `hiep`, `hieu`).
   - **Không đẩy trực tiếp lên nhánh `main`**.

2. **Pull mã nguồn trước khi làm việc**:
   - Đảm bảo mã nguồn luôn cập nhật:
     ```bash
     git pull origin main
     ```

3. **Commit và đẩy mã lên nhánh cá nhân**:
   - Chuyển sang nhánh cá nhân:
     ```bash
     git checkout <tên_nhánh_cá_nhân>
     ```
   - Commit và đẩy mã:
     ```bash
     git add .
     git commit -m "Mô tả thay đổi"
     git push origin <tên_nhánh_cá_nhân>
     ```

4. **Gửi yêu cầu hợp nhất vào nhánh `main`**:
   - Tạo Pull Request (PR) sau khi hoàn thiện công việc để được xem xét.

---

## Một số lưu ý

1. **Cài đặt thư viện mới**:
   - Nếu cần cài đặt thêm thư viện PHP:
     ```bash
     composer require <tên_thư_viện>
     ```
   - Commit các thay đổi trong file `composer.json` và `composer.lock`.

2. **Liên hệ**:
   - Nếu gặp vấn đề, liên hệ **Nguyễn Hữu Thắng** hoặc các thành viên khác để được hỗ trợ.

---
