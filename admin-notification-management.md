# Kế hoạch Triển khai: Quản lý Thông báo (Admin) - CareBook

## 1. Phân tích Yêu cầu (Analysis)
Hệ thống thông báo cho phép Admin:
- **Tạo thông báo thủ công**: Lọc người nhận, lên nội dung, chọn kênh (in_web, email), và đặt lịch gửi (gửi ngay hoặc hẹn giờ).
- **Xem lịch sử**: Danh sách thông báo được nhóm (GROUP BY) theo chiến dịch (cùng tiêu đề, nội dung, loại) để tránh lặp dữ liệu do gửi hàng loạt.
- **Xử lý nền (Background Processing)**: 
  - Đẩy vào DB hàng loạt bằng `insert()`.
  - Queue job (Job) gửi email thông qua SMTP.
  - Scheduled command (Cron Job) quét thông báo hẹn giờ để đẩy vào Queue.

## 2. Kế hoạch (Planning)

### 2.1 Cơ sở dữ liệu (Database)
Bảng `notifications` đã/sẽ có các field:
- `user_id`: Người nhận
- `title`, `content`: Nội dung thông báo
- `type`: ENUM ('appointment', 'result', 'reminder', 'system')
- `channel`: ENUM ('in_web', 'email', 'zalo')
- `is_sent`: boolean (0 = chưa gửi, 1 = đã gửi) - Dùng cho email/zalo
- `is_read`: boolean (0 = chưa đọc, 1 = đã đọc) - Dùng cho in_web
- `scheduled_at`: datetime (thời gian hẹn giờ gửi, nullable)
- `ref_type`, `ref_id`: đa hình (tùy chọn để link tới Lịch hẹn/Hồ sơ khám)

### 2.2 Các lớp (Classes) cần tạo/cập nhật
1. **Controller**: `Admin\NotificationController`
   - `index()`: Gom nhóm `GROUP BY` để lấy danh sách chiến dịch thông báo.
   - `create()`: Render form (đã hoàn thiện).
   - `store()`: Xử lý logic expand người nhận x kênh gửi và `insert()` hàng loạt.
   - `destroy()`: Hủy thông báo chưa gửi (của chiến dịch).
2. **Job**: `SendEmailNotificationJob`
   - Nhận ID thông báo (hoặc danh sách), gửi qua `Mail::to()` và update `is_sent = 1`.
3. **Console Command**: `ProcessScheduledNotifications`
   - Quét DB tìm `scheduled_at <= now()` và `is_sent = 0` để đẩy vào Job. Đăng ký trong `Console/Kernel.php` hoặc `routes/console.php`.

### 2.3 Luồng thực thi (Execution Flow)
- **Store**: Xác thực Form Request -> Tạo mảng `data[]` chứa thông tin -> Dùng `Notification::insert($data)` -> Khởi chạy Event/Job nếu cần gửi Email ngay.
- **Index**: 
  ```php
  Notification::selectRaw('title, content, type, scheduled_at, created_at, COUNT(*) as total_recipients, SUM(is_sent) as sent_count, SUM(is_read) as read_count')
    ->groupBy('title', 'content', 'type', 'scheduled_at', 'created_at')
    ->paginate(15);
  ```

## 3. Các bước Thực hiện (Implementation Steps)
- [ ] **Bước 1**: Cập nhật Model và Migration `Notification` (kiểm tra và bổ sung field nếu thiếu).
- [ ] **Bước 2**: Viết `NotificationRequest` để validate đầu vào từ Admin.
- [ ] **Bước 3**: Viết logic `store()` trong `NotificationController` thực hiện Bulk Insert.
- [ ] **Bước 4**: Tạo `SendEmailNotificationJob` và Email Mailable/Template.
- [ ] **Bước 5**: Viết logic `index()` sử dụng Query Builder để Group dữ liệu và thay đổi UI Index (để phù hợp với dạng chiến dịch thay vì hiển thị đơn lẻ từng bản ghi).
- [ ] **Bước 6**: Tạo `Schedule` command chạy hàng phút để kích hoạt thông báo hẹn giờ.

> 🔴 **Lưu ý**: Cần chuyển Index UI từ hiển thị "Từng bản ghi (đơn lẻ)" sang "Chiến dịch (Gom nhóm)" như yêu cầu để tránh quá tải danh sách.
