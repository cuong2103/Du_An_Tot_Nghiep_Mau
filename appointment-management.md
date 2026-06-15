# Kế hoạch Triển khai: Module Quản lý Lịch hẹn (Appointment Management)

Mục tiêu: Xây dựng luồng quản lý lịch hẹn (CRUD) xuyên suốt từ khi bệnh nhân đặt lịch đến khi bác sĩ/admin xử lý cho dự án Carebook. Đảm bảo hiệu năng, tính nhất quán dữ liệu (tránh double-booking) và trải nghiệm người dùng tốt mà **KHÔNG thay đổi CSDL hiện tại**.

## User Review Required

> [!IMPORTANT]
> **Cơ chế Lock Slot (Tránh trùng lịch):** Vẫn giữ nguyên đề xuất sử dụng **Laravel Cache Lock** (`Cache::lock`) trên Redis/File để khóa slot tạm thời trong 5-10 phút. Không cần bảng tạm trong Database.

## Open Questions

- Đối với giao diện Calendar View của Admin/Lễ tân, bạn muốn tích hợp thư viện Javascript nào (ví dụ: `FullCalendar.io`) hay tự build UI bằng CSS Grid của Tailwind?

## Proposed Changes

---

### Tầng Dữ liệu & Kiến trúc (Database Architect)

- **KHÔNG CẦN CHỈNH SỬA DATABASE MIGRATION.** Tất cả sẽ dựa vào cấu trúc bảng `appointments`, `users`, `work_schedules` hiện tại. Trạng thái `cancelled` (đã hủy) đã có sẵn trong model `Appointment`.

---

### Tầng Xử lý Logic (Backend Specialist)

Xây dựng luồng xử lý giao dịch an toàn, áp dụng nguyên tắc nguyên tử (Atomicity) cho việc đặt lịch.

#### [NEW] app/Services/AppointmentService.php
- Tạo service chuyên biệt xử lý logic đặt lịch.
- Logic **Lock Slot**: Sử dụng `Cache::lock('slot:'.$doctorId.':'.$date.':'.$time, 10 * 60)` để giữ chỗ trong 10 phút.
- Logic **Tự động xác nhận**: Set mặc định `status = 'confirmed'` hoặc `pending` nhưng coi như đã chốt sau khi validate thành công.
- Logic **Hủy lịch**: Kiểm tra `$appointment->appointment_date` có cách thời điểm hiện tại hơn 12 tiếng không. Nếu lớn hơn 12h -> cập nhật `status = 'cancelled'`. Nếu nhỏ hơn 12h -> báo lỗi không cho phép.

#### [NEW] app/Http/Controllers/Api/AppointmentController.php
- API cho bệnh nhân tự đặt lịch / hủy lịch từ phía ứng dụng (nếu có). 

#### [MODIFY] app/Http/Controllers/Admin/AppointmentController.php
- Cung cấp các method CRUD cho Admin/Lễ tân (Danh sách, Chi tiết, Cập nhật trạng thái).
- Xử lý Action chuyển trạng thái lịch hẹn sang `absent` khi bệnh nhân không đến.

---

### Tầng Giao diện Admin/Lễ tân (Frontend Specialist)

Xây dựng giao diện trực quan để thao tác với lịch hẹn.

#### [NEW] resources/views/admin/appointments/index.blade.php
- Bảng danh sách lịch hẹn trong ngày / tuần.
- Bộ lọc theo: Trạng thái, Bác sĩ, Ngày khám, Chuyên khoa.
- Nút tác vụ nhanh: Hủy lịch, Cập nhật trạng thái (Đã khám, Vắng mặt).

#### [NEW] resources/views/admin/appointments/calendar.blade.php
- Giao diện Calendar View giúp Lễ tân nhìn tổng quan các ca khám trong tuần.
- Highlight các ca khám bằng màu sắc dựa trên trạng thái (Vàng = Chờ, Xanh dương = Đã tiếp nhận, Xanh lá = Hoàn thành, Đỏ = Đã hủy, Xám = No-show).

#### [NEW] resources/views/admin/appointments/show.blade.php
- Chi tiết lịch hẹn, hiển thị thông tin bệnh nhân, bác sĩ, thời gian và lịch sử thay đổi trạng thái (Thông qua table `appointment_logs` nếu có, hoặc chỉ hiển thị logs đơn thuần).

## Verification Plan

### Automated Tests
- Viết Unit Test cho `AppointmentService`:
  - Thử nghiệm 2 request cùng lúc đặt vào 1 slot -> đảm bảo request thứ 2 bị lỗi `Slot is already locked`.
  - Thử nghiệm hủy lịch trước 12h -> Thành công.
  - Thử nghiệm hủy lịch trước 5h -> Thất bại.
  - Thử nghiệm tạo bệnh nhân có 3 lịch `absent` -> bị từ chối khi đặt lịch mới.

### Manual Verification
- Đăng nhập quyền Lễ tân, chuyển đổi trạng thái 3 lịch hẹn của 1 bệnh nhân sang `Vắng mặt` (No-show). Dùng account bệnh nhân đó thử đặt lịch mới -> kỳ vọng nhận thông báo lỗi bị chặn đặt lịch.
- Kiểm tra giao diện Calendar view có render đúng màu sắc của các slot đã được đặt hay không.
