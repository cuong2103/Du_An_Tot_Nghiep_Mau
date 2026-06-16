# Kế hoạch Triển khai: Luồng Đặt Lịch Cho Khách (Guest Booking Flow)

## 1. Mục tiêu
Cho phép người dùng chưa đăng nhập (Guest) hoàn thành Bước 1 (Chọn chuyên khoa), Bước 2 (Chọn bác sĩ & giờ khám), và nhập "Lý do khám" ở Bước 3. 
Hệ thống sẽ tạm lưu tiến trình này. Sau khi người dùng Đăng nhập / Đăng ký thành công, họ sẽ được đưa trở lại trang Đặt lịch, khôi phục ngay tiến trình cũ (nhảy thẳng đến Bước 3), chọn Hồ sơ bệnh nhân và sang Bước 4 (Xác nhận).

## 2. Thông tin & Yêu cầu Kỹ thuật
- **Lưu trữ tiến trình**: Sử dụng `sessionStorage` (hoặc `localStorage`) trong AlpineJS để lưu state tạm thời.
- **Điều hướng Auth**: Sử dụng cơ chế `URL.intended` của Laravel để redirect back về `/dat-lich` sau khi login.

## 3. Phân chia Công việc (Task Breakdown)

### Phase 1: Cập nhật Giao diện Bước 3 (Guest Mode)
- [ ] Chỉnh sửa `resources/views/patient/booking/index.blade.php`.
- [ ] Ở Bước 3, nếu `@guest`, vẫn cho phép nhập "Lý do khám / Triệu chứng".
- [ ] Ẩn phần chọn "Hồ sơ bệnh nhân" nếu là guest.
- [ ] Thay nút "Xem xác nhận" thành nút "Đăng nhập để tiếp tục", trỏ link tới `/dang-nhap`.

### Phase 2: Lưu và Khôi phục State với AlpineJS
- [ ] Cập nhật Alpine component `bookingApp()`: thêm logic `watch` các biến `selectedSpecialty`, `selectedDoctor`, `selectedDate`, `selectedSlot`, `reason` để lưu vào `sessionStorage`.
- [ ] Trong hàm `init()`, kiểm tra nếu có data trong `sessionStorage`, tự động gán lại các biến này.
- [ ] Tính toán tự động chuyển `step = 3` nếu đã có đủ thông tin bác sĩ, ngày, giờ.

### Phase 3: Xóa State sau khi Đặt lịch thành công
- [ ] Sau khi submit form thành công, thêm script xóa `sessionStorage` ở trang `success.blade.php` (hoặc xóa ngay khi bấm Xác nhận) để tránh việc lần sau vào lại bị dính dữ liệu cũ.

### Phase 4: Kiểm tra Auth Controller
- [ ] Đảm bảo middleware `guest` hoặc `AuthController` của Laravel redirect user quay lại đúng trang `/dat-lich` thay vì luôn văng về `/dashboard` hoặc `/` (Laravel mặc định hỗ trợ `session()->put('url.intended')` nếu dùng Middleware `auth` chặn, nhưng ở đây `/dat-lich` là public route nên ta có thể truyền `?redirect=/dat-lich` hoặc gán thủ công).
