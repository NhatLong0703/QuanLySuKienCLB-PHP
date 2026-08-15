# Hệ thống quản lý sự kiện / câu lạc bộ sinh viên

## 1. Thành viên nhóm
| MSV | Họ và tên | Vai trò | Nhiệm vụ |
| --- | --- | --- | --- |
| 224001778 | Đặng Quang Doanh (Nhóm trưởng) | Project Manager | Thiết kế database, quản lý dự án, triển khai tính năng, phân tích nghiệp vụ |
| 224001811 | Nguyễn Nhật Long | Backend | Code backend, test tính năng |
| 224001829 | Cao Bá Sơn | Frontend | UI, Figma, code frontend |
| 224001815 | Nguyễn Đức Minh | Backend | Code backend, test API |
| 224001775 | Dương Thị Chi | Frontend | UI, Figma, code frontend |
| 223001676 | Nguyễn Phương Thùy | QA / Documentation | Viết báo cáo, Figma, test dự án |

## 2. Mô tả bài toán
Xây dựng cổng thông tin cho câu lạc bộ hoặc khoa để công bố sự kiện, nhận đăng ký, điểm danh và thống kê người tham gia.

### Người dùng mục tiêu
- **Khách / Thành viên**: Xem danh sách sự kiện, lọc theo ngày/câu lạc bộ, xem chi tiết, đăng ký hoặc hủy đăng ký sự kiện.
- **Ban tổ chức (BTC)**: Tạo sự kiện, quản lý đăng ký, đóng mở đăng ký, điểm danh người tham gia và xem danh sách tham gia.
- **Quản trị viên (Admin)**: Quản lý câu lạc bộ, tài khoản người dùng, toàn bộ sự kiện và thống kê hệ thống.

### Luồng nghiệp vụ chính
1. **Khách / Thành viên**: Duyệt danh sách sự kiện → Lọc theo thời gian, câu lạc bộ, trạng thái → Xem chi tiết → Đăng ký tham gia (kiểm tra hạn, số chỗ, trùng lặp) → Hủy đăng ký trước hạn.
2. **Ban tổ chức**: Tạo/Sửa sự kiện thuộc câu lạc bộ quản lý → Đóng/Mở đăng ký → Quản lý người tham gia → Điểm danh qua Fetch API hoặc mã QR → Xem thống kê tham gia.
3. **Quản trị viên**: Quản lý câu lạc bộ và tài khoản → Theo dõi thống kê tổng quan toàn hệ thống.

### Thống nhất các đối tượng dữ liệu và chức năng chính của hệ thống.

#### 1. Các đối tượng dữ liệu chính
Hệ thống xoay quanh 8 bảng dữ liệu cốt lõi:
- **Users**: Lưu trữ thông tin người dùng (Thành viên, Ban tổ chức, Admin) bao gồm mật khẩu mã hóa an toàn (Bcrypt).
- **Clubs**: Thông tin các Câu lạc bộ tham gia tổ chức sự kiện.
- **Events**: Thông tin chi tiết về các sự kiện (thời gian, địa điểm, sức chứa...).
- **Registrations**: Lưu trữ các lượt đăng ký tham gia sự kiện của thành viên.
- **Club_Managers**: Lưu trữ quyền quản lý CLB (liên kết User và Club).
- **Attendance**: Lưu trữ lịch sử điểm danh (check-in) thực tế của người tham gia.
- **Notifications**: Lưu trữ thông báo gửi tới người dùng liên quan đến sự kiện.
- **Audit_Logs**: Nhật ký hệ thống ghi lại mọi thao tác quan trọng để Admin dễ dàng truy vết.

#### 2. Các chức năng dự kiến
- **Dành cho Thành viên (Member)**:
  - Đăng ký tài khoản, đăng nhập an toàn.
  - Xem danh sách sự kiện, lọc và tìm kiếm sự kiện.
  - Xem chi tiết sự kiện và thực hiện Đăng ký / Hủy đăng ký.
  - Xem thông báo (Notifications) về các sự kiện quan tâm.
- **Dành cho Ban tổ chức (Organizer)**:
  - Tất cả quyền của Member.
  - Tạo mới và quản lý sự kiện do CLB của mình tổ chức (bao gồm Upload ảnh minh họa).
  - Quản lý danh sách người tham gia, thực hiện Điểm danh (Check-in).
  - Trích xuất danh sách tham gia ra file **Excel**, **PDF** hoặc Import danh sách.
  - Đăng thông báo mới cho sự kiện.
- **Dành cho Quản trị viên (Admin)**:
  - Toàn quyền quản trị hệ thống.
  - Quản lý tài khoản (Khóa/Mở khóa người dùng).
  - Quản lý danh sách CLB, Cấp/Thu hồi quyền quản lý (Organizer) cho người dùng.
  - Xem và kiểm soát toàn bộ nhật ký hệ thống (Audit logs).

## 3. Công nghệ sử dụng
- PHP 8.x (kiến trúc MVC thuần)
- MySQL
- PDO (Prepared Statements)
- JavaScript / Fetch API
- HTML5 / CSS3 (responsive)

## 4. Cài đặt
1. Clone repository:
   ```bash
   git clone https://github.com/NhatLong0703/QuanLySuKienCLB-PHP.git
   cd QuanLySuKienCLB-PHP
   ```
2. Cấu hình kết nối cơ sở dữ liệu trong `config/config.php`.
3. Tạo database và import dữ liệu từ file SQL nếu có.
4. Khởi động server PHP hoặc cấu hình cho máy chủ web của bạn:
   ```bash
   php -S localhost:8000 -t public
   ```
5. Truy cập: `http://localhost:8000`

## 5. Ghi chú
- Chú ý chỉnh sửa `config/config.php` với thông tin database đúng.
- Nếu có file dữ liệu mẫu, import vào MySQL trước khi chạy ứng dụng.

