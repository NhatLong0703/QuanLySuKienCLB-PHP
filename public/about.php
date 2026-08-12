
<?php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Giới thiệu - Hệ thống quản lý sự kiện</title>
	<style>
		body{font-family:Arial,Helvetica,sans-serif;background:#f7f7f7;color:#222;margin:0;padding:0}
		.container{max-width:900px;margin:28px auto;background:#fff;padding:24px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.06)}
		h1,h2{color:#1a73e8}
		table{width:100%;border-collapse:collapse;margin:8px 0}
		th,td{border:1px solid #e6e6e6;padding:8px;text-align:left}
		th{background:#fafafa}
		pre {background:#f4f4f4;padding:10px;border-radius:4px;overflow:auto}
		a.button{display:inline-block;padding:8px 12px;background:#1a73e8;color:#fff;border-radius:4px;text-decoration:none}
	</style>
</head>
<body>
<div class="container">
	<h1>Hệ thống quản lý sự kiện / câu lạc bộ sinh viên</h1>

	<h2>1. Thành viên nhóm</h2>
	<table>
		<thead>
			<tr><th>MSV</th><th>Họ và tên</th><th>Vai trò</th><th>Nhiệm vụ</th></tr>
		</thead>
		<tbody>
			<tr><td>224001778</td><td>Đặng Quang Doanh</td><td>Project Manager</td><td>Thiết kế database, quản lý dự án, triển khai tính năng, phân tích nghiệp vụ</td></tr>
			<tr><td>224001811</td><td>Nguyễn Nhật Long</td><td>Backend</td><td>Code backend, test tính năng</td></tr>
			<tr><td>224001829</td><td>Cao Bá Sơn</td><td>Frontend</td><td>UI, Figma, code frontend</td></tr>
			<tr><td>224001815</td><td>Nguyễn Đức Minh</td><td>Backend</td><td>Code backend, test API</td></tr>
			<tr><td>224001775</td><td>Dương Thị Chi</td><td>Frontend</td><td>UI, Figma, code frontend</td></tr>
			<tr><td>223001676</td><td>Nguyễn Phương Thùy</td><td>QA / Documentation</td><td>Viết báo cáo, Figma, test dự án</td></tr>
		</tbody>
	</table>

	<h2>2. Mô tả bài toán</h2>
	<p>Xây dựng cổng thông tin cho câu lạc bộ hoặc khoa để công bố sự kiện, nhận đăng ký, điểm danh và thống kê người tham gia.</p>

	<h2>Người dùng mục tiêu</h2>
	<ul>
		<li><strong>Khách / Thành viên</strong>: Xem danh sách sự kiện, lọc, xem chi tiết, đăng ký/hủy đăng ký.</li>
		<li><strong>Ban tổ chức (BTC)</strong>: Tạo sự kiện, quản lý đăng ký, đóng/mở đăng ký, điểm danh và thống kê.</li>
		<li><strong>Quản trị viên (Admin)</strong>: Quản lý câu lạc bộ, tài khoản người dùng và toàn bộ hệ thống.</li>
	</ul>

	<h2>Luồng nghiệp vụ chính</h2>
	<ol>
		<li>Khách/Thành viên: Duyệt → Lọc → Xem → Đăng ký/Hủy đăng ký.</li>
		<li>Ban tổ chức: Tạo/Sửa sự kiện → Quản lý đăng ký → Điểm danh → Thống kê.</li>
		<li>Quản trị viên: Quản lý câu lạc bộ và tài khoản, theo dõi thống kê hệ thống.</li>
	</ol>

	<h2>Công nghệ sử dụng</h2>
	<p>PHP 8.x (MVC thuần), MySQL, PDO, JavaScript / Fetch API, HTML5/CSS3.</p>

	<h2>Cài đặt nhanh</h2>
	<p>Thực hiện các bước sau để chạy trên máy local:</p>
	<pre>git clone https://github.com/NhatLong0703/QuanLySuKienCLB-PHP.git
cd QuanLySuKienCLB-PHP
chỉnh sửa file config/config.php với thông tin database
php -S localhost:8000 -t public</pre>

	<p>Truy cập <a href="/">Trang chủ</a> hoặc mở trang này tại: <a href="/about.php">Giới thiệu</a>.</p>

	<h2>Ghi chú</h2>
	<ul>
		<li>Chỉnh `config/config.php` với thông tin database chính xác trước khi chạy.</li>
		<li>Import dữ liệu mẫu vào MySQL nếu có file SQL kèm theo.</li>
	</ul>

	<p style="margin-top:18px"><a class="button" href="/">Quay về trang chủ</a></p>
</div>
</body>
</html>
