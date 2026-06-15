# Kế hoạch Triển khai Chức năng cho Role Bệnh Nhân (Patient)

Mục tiêu: Xây dựng trải nghiệm trọn vẹn cho người dùng cuối (Bệnh nhân) từ lúc truy cập website, tra cứu thông tin, đặt lịch khám, cho đến khi theo dõi hồ sơ bệnh án sau khi khám xong.

## 1. Phân tích Hiện trạng (Context)
Hệ thống Admin (Quản trị viên) đã khá hoàn thiện với các module Quản lý Lịch làm việc, Thông báo, Chatbot, và Cấu hình hệ thống. Bây giờ, chúng ta sẽ xây dựng luồng nghiệp vụ (Business Logic) mặt Frontend cho Bệnh nhân tương tác. 
Các bảng dữ liệu cốt lõi đã có: `users` (role=patient), `patient_profiles`, `appointments`, `medical_records`, `prescriptions`, `reviews`.

## 2. Phân rã Nhiệm vụ (Task Breakdown)

### Phase 1: Xác thực & Hồ sơ cá nhân (Auth & Profile)
- Đăng ký / Đăng nhập / Quên mật khẩu (Có thể xác thực qua Email hoặc SĐT).
- **Trang Dashboard Bệnh nhân (My Account)**: Nơi bệnh nhân xem tổng quan.
- **Quản lý Hồ sơ (`patient_profiles`)**: Cập nhật thông tin cơ bản (Nhóm máu, Tiền sử bệnh lý, Dị ứng thuốc).

### Phase 2: Luồng Đặt lịch khám (Booking Flow) - *Core Feature*
- **Bước 1 - Tìm kiếm & Lọc**: Bệnh nhân tìm bác sĩ theo Chuyên khoa, theo Tên, hoặc xem bác sĩ nào đang có lịch trống.
- **Bước 2 - Chọn thời gian**: Xem lịch làm việc thực tế của bác sĩ (dựa vào `work_schedules`). Chỉ hiển thị các khung giờ chưa bị đầy (kiểm tra `max_appointment_per_slot` trong `system_settings`).
- **Bước 3 - Điền thông tin khám**: Nhập triệu chứng ban đầu.
- **Bước 4 - Xác nhận**: Hệ thống tạo bản ghi vào bảng `appointments` với trạng thái `pending` (Chờ xác nhận).

### Phase 3: Quản lý Lịch hẹn & Lịch sử khám
- **Danh sách Lịch hẹn**: Xem các lịch sắp tới, lịch đã hủy.
- **Hủy lịch**: Cho phép bệnh nhân tự hủy lịch nếu thời gian hiện tại cách giờ khám lớn hơn `cancel_before_hours` (lấy từ cài đặt hệ thống).
- **Hồ sơ bệnh án & Đơn thuốc**: Sau khi bác sĩ khám xong, bệnh nhân có thể vào xem toa thuốc (`prescriptions`) và kết luận lâm sàng (`medical_records`).

### Phase 4: Tương tác mở rộng
- **Chatbot AI**: Tích hợp module Gemini Chatbot ra giao diện góc dưới màn hình để bệnh nhân hỏi đáp nhanh.
- **Đánh giá (Reviews)**: Bệnh nhân được phép đánh giá (1-5 sao) và viết nhận xét cho Bác sĩ sau khi ca khám chuyển trạng thái hoàn tất (`completed`).
- **Thông báo**: Xem các thông báo từ Admin (nhắc lịch khám, tin tức).

---

## 3. Cổng Socratic (Socratic Gate)

> [!WARNING]
> Để thiết kế chính xác luồng nghiệp vụ cho Patient, tôi cần bạn xác nhận các câu hỏi sau:
> 1. **Thanh toán**: Bệnh nhân có cần thanh toán tiền khám (hoặc đặt cọc) qua cổng thanh toán online (VNPay, MoMo) lúc đặt lịch không, hay chỉ cần đặt lịch rồi **đến phòng khám mới thanh toán**? -> đến phòng khám thanh toán
> 2. **Đặt lịch hộ**: Một tài khoản bệnh nhân có được phép tạo nhiều "Hồ sơ người bệnh" để đặt lịch khám cho người thân (con cái, ba mẹ) không, hay 1 tài khoản chỉ đại diện cho đúng 1 người khám? ->có nhiều hồ sơ người bệnh
> 3. **Giao diện (UI)**: Chức năng dành cho Bệnh nhân sẽ nằm trên giao diện **Trang chủ (Client/Front-end)** hay bạn muốn làm một **Dashboard** giống Admin nhưng thu gọn lại? ->giao diện mới dễ tiếp cận với bệnh nhân

---

> Kế hoạch đã được phác thảo. Hãy trả lời các câu hỏi trên và gõ `@[/create]` để tiến hành!
