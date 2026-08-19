const API_BASE = '/api';
function getUser() { const u=localStorage.getItem('user'); return u?JSON.parse(u):null; }
function requireLogin() { if(!getUser()) window.location.href='/views/auth/login.html'; }
function requireRole(role) { const u=getUser(); if(!u||(u.role!==role&&u.role!=='admin')){ alert('Ban khong co quyen truy cap!'); window.location.href='/views/auth/login.html'; } }
function logout() { localStorage.removeItem('user'); window.location.href='/views/auth/login.html'; }
function setupNavbar() {
    const u=getUser();
    const nav=document.createElement('div'); nav.className='navbar';
    nav.innerHTML=`<div><strong>CLB Event</strong>
        ${u&&u.role==='admin'?'<a href="/views/admin/dashboard.html">Admin</a>':''}
        ${u&&u.role==='organizer'?'<a href="/views/organizer/dashboard.html">Organizer</a>':''}
        ${u?'<a href="/views/member/events.html">Sự kiện</a>':''}
    </div><div>
        ${u?`<a href="#" onclick="showNotifications()" style="margin-right:15px; font-size:18px;" title="Thông báo">🔔</a>
             <span>Chào, ${u.full_name} (${u.role})</span> 
             <a href="#" onclick="logout()">Đăng xuất</a>`
           :'<a href="/views/auth/login.html">Đăng nhập</a>'}
    </div>`;
    document.body.prepend(nav);

    // Modal for Notifications
    if(u) {
        const notiModal = document.createElement('div');
        notiModal.id = 'notiModal';
        notiModal.style.cssText = 'display:none; position:fixed; right:20px; top:60px; width:300px; background:white; border:1px solid #ccc; box-shadow:0 4px 8px rgba(0,0,0,0.1); border-radius:5px; z-index:1000; padding:15px; max-height:400px; overflow-y:auto;';
        notiModal.innerHTML = `<h4>Thông báo</h4><div id="notiContent"><small>Đang tải...</small></div><button onclick="document.getElementById('notiModal').style.display='none'" style="margin-top:10px; width:100%; border:none; background:#eee; padding:5px; cursor:pointer;">Đóng</button>`;
        document.body.appendChild(notiModal);
    }
}

async function showNotifications() {
    const modal = document.getElementById('notiModal');
    modal.style.display = modal.style.display === 'none' ? 'block' : 'none';
    if(modal.style.display === 'block') {
        try {
            const res = await apiCall('/notification/list?limit=10');
            const content = document.getElementById('notiContent');
            if(!res.data.data.length) {
                content.innerHTML = '<small>Không có thông báo mới.</small>';
                return;
            }
            content.innerHTML = res.data.data.map(n => `<div style="border-bottom:1px solid #eee; padding:8px 0;">
                <strong style="display:block; font-size:14px; margin-bottom:4px;">${n.title}</strong>
                <p style="margin:0; font-size:12px; color:#555;">${n.content}</p>
                <small style="color:#999; font-size:10px;">${n.created_at} - <i>${n.author_name}</i></small>
            </div>`).join('');
        } catch(e) {
            console.error(e);
        }
    }
}
async function apiCall(endpoint, method = 'GET', data = null) {
    const opts = { method, headers: {} };
    
    // Add mock Authorization header from localStorage
    const uStr = localStorage.getItem('user');
    if (uStr) {
        // Base64 encode the user JSON string to create a mock JWT
        const mockToken = btoa(unescape(encodeURIComponent(uStr)));
        opts.headers['Authorization'] = 'Bearer ' + mockToken;
    }
    
    if (data) {
        if (data instanceof FormData) {
            // Let the browser set Content-Type with boundary automatically for FormData
            opts.body = data;
        } else {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(data);
        }
    }
    
    const res = await fetch(API_BASE + endpoint, opts);
    const json = await res.json();
    if (json.status === 'error') throw new Error(json.message);
    return json;
}

// --- THEME & I18N SYSTEM ---
document.addEventListener('DOMContentLoaded', () => {
    applyTheme();
    applyI18n();
});

function getRole() {
    let role = 'guest';
    const uStr = localStorage.getItem('user');
    if (uStr) {
        try {
            const u = JSON.parse(uStr);
            if (u && u.role) role = u.role;
        } catch(e) {}
    }
    return role;
}

function applyTheme() {
    const role = getRole();
    const mode = localStorage.getItem(`clubhub_theme_${role}`) || 'light';
    const accentName = localStorage.getItem(`clubhub_accent_${role}`) || 'emerald';
    
    // Map accent names to hex colors
    const accents = {
        'emerald': '#10B981',
        'amber': '#F59E0B',
        'blue': '#3B82F6',
        'purple': '#8B5CF6'
    };
    const accentHex = accents[accentName] || '#10B981';
    
    const style = document.createElement('style');
    let css = `
        /* Override Accent */
        .sidebar-menu a.active { background-color: ${accentHex} !important; border-color: ${accentHex} !important; }
        .tab-btn.active { background: ${accentHex} !important; border-color: ${accentHex} !important; }
        .recent-header a { color: ${accentHex} !important; }
        .btn-register, .btn-primary { background: ${accentHex} !important; border-color: ${accentHex} !important; }
        .stat-card.accent { background: ${accentHex} !important; }
        .form-control:focus { border-color: ${accentHex} !important; box-shadow: 0 0 0 3px ${accentHex}33 !important; }
        .badge-active { color: ${accentHex} !important; background-color: ${accentHex}15 !important; }
    `;
    
    if (mode === 'dark') {
        css += `
            body { background-color: #111827 !important; color: #F3F4F6 !important; }
            .main-content { background-color: #111827 !important; }
            .stat-card, .table-container, .club-card, .recent-card, .noti-card, .noti-detail { 
                background-color: #1F2937 !important; 
                border-color: #374151 !important; 
                color: #F3F4F6 !important;
            }
            .header h1, .stat-value, .detail-title, .recent-header h3, .club-title, th, td { color: #F3F4F6 !important; }
            .sidebar { border-right: 1px solid #374151 !important; }
            table td { border-bottom-color: #374151 !important; }
            th { border-bottom-color: #374151 !important; }
            .tab-btn { background: #374151 !important; color: #D1D5DB !important; border-color: #4B5563 !important; }
            .tab-btn:hover:not(.active) { background: #4B5563 !important; }
            .search-bar { background: #374151 !important; color: white !important; border-color: #4B5563 !important; }
        `;
    }
    
    // Add print styles globally
    css += `
        @media print {
            .sidebar, .tabs, .header p, .action-bar { display: none !important; }
            .main-content { padding: 0 !important; width: 100% !important; margin: 0 !important; }
            body { background: white !important; color: black !important; }
            .table-container { box-shadow: none !important; border: none !important; }
            table { width: 100% !important; }
            th, td { border-bottom: 1px solid #ccc !important; color: black !important; padding: 8px !important; }
            .badge { border: 1px solid #ccc !important; background: transparent !important; color: black !important; }
        }
    `;
    
    style.innerHTML = css;
    document.head.appendChild(style);
}

const translations = {
    'en': {},
    'vi': {
        'Dashboard': 'Tổng quan',
        'Clubs': 'Câu lạc bộ',
        'Events': 'Sự kiện',
        'Registrations': 'Đăng ký',
        'Attendance': 'Điểm danh',
        'Notifications': 'Thông báo',
        'Audit Log': 'Nhật ký hệ thống',
        'System': 'Hệ thống',
        'Users': 'Thành viên',
        'Settings': 'Cài đặt',
        'Language': 'Ngôn ngữ',
        'Navigation': 'Điều hướng',
        'Edit Club': 'Sửa Câu lạc bộ',
        'Add New Club': 'Thêm CLB Mới',
        'Club Name': 'Tên Câu lạc bộ',
        'Description': 'Mô tả',
        'Image': 'Hình ảnh',
        'Status': 'Trạng thái',
        'Cancel': 'Hủy',
        'Save Club': 'Lưu Câu lạc bộ',
        'Active': 'Hoạt động',
        'Inactive': 'Tạm dừng',
        'Edit Event': 'Sửa Sự kiện',
        'Add New Event': 'Thêm Sự kiện Mới',
        'Event Title': 'Tên Sự kiện',
        'Club': 'Câu lạc bộ',
        'Location': 'Địa điểm',
        'Event Image': 'Ảnh Sự kiện',
        'Start Time': 'Thời gian Bắt đầu',
        'End Time': 'Thời gian Kết thúc',
        'Capacity': 'Số lượng tối đa',
        'Deadline': 'Hạn chót',
        'Draft': 'Bản nháp',
        'Open (Upcoming)': 'Mở đăng ký',
        'Closed (Completed)': 'Đã đóng (Hoàn thành)',
        'Cancelled': 'Đã hủy',
        'Save Event': 'Lưu Sự kiện',
        'Profile & Settings': 'Hồ sơ & Cài đặt',
        'Manage your account details and application preferences.': 'Quản lý thông tin tài khoản và cấu hình hệ thống.',
        'Personal Information': 'Thông tin Cá nhân',
        'Full Name': 'Họ và Tên',
        'Email': 'Email',
        '(Read-only)': '(Chỉ xem)',
        'Phone Number': 'Số điện thoại',
        'New Password': 'Mật khẩu mới',
        '(Leave blank to keep current)': '(Bỏ trống nếu không đổi)',
        'Save Changes': 'Lưu Thay Đổi',
        'Application Preferences': 'Cài đặt Ứng dụng',
        'Appearance (Theme)': 'Giao diện',
        'Switch between Light and Dark mode.': 'Chuyển đổi giữa chế độ Sáng và Tối.',
        'Accent Color': 'Màu Chủ Đạo',
        'Personalize the primary color.': 'Tùy chỉnh màu sắc nổi bật.',
        'Choose your preferred language.': 'Chọn ngôn ngữ hiển thị.',
        'Light Mode': 'Nền Sáng',
        'Dark Mode': 'Nền Tối',
        'Emerald Green (Default)': 'Xanh Lục (Mặc định)',
        'Amber Yellow': 'Vàng Hổ Phách',
        'Ocean Blue': 'Xanh Đại Dương',
        'Royal Purple': 'Tím Hoàng Gia',
        'English (Default)': 'Tiếng Anh (Mặc định)',
        'Welcome back!': 'Chào mừng trở lại!',
        'My Registered': 'Đã Đăng Ký',
        'Total events joined': 'Sự kiện đã tham gia',
        'Upcoming': 'Sắp tới',
        'Events I will attend': 'Sự kiện sắp tham dự',
        'Attended': 'Đã Tham Gia',
        'Confirmed check-ins': 'Đã điểm danh',
        'Open Events': 'Sự kiện Mở',
        'Available to register': 'Có thể đăng ký',
        'Your Next Event': 'Sự kiện tiếp theo',
        'Events You Might Like': 'Sự kiện gợi ý',
        'Browse all →': 'Xem tất cả →',
        'Search...': 'Tìm kiếm...',
        'Filter by Status': 'Lọc trạng thái',
        'All': 'Tất cả',
        'Role': 'Vai trò',
        'Action': 'Thao tác',
        'Edit': 'Sửa',
        'Delete': 'Xóa',
        'Browse Clubs': 'Khám phá Câu lạc bộ',
        'Browse Events': 'Khám phá Sự kiện',
        'My Registrations': 'Sự kiện của tôi',
        'Explore': 'Khám phá',
        'My Activity': 'Hoạt động cá nhân',
        'Personal': 'Cá nhân',
        'Administrator': 'Quản trị viên',
        'Member Portal': 'Cổng Thành viên',
        'Organizer Portal': 'Cổng Ban tổ chức',
        'Admin Portal': 'Cổng Quản trị',
        'Dashboard': 'Bảng điều khiển',
        'Clubs': 'Câu lạc bộ',
        'Events': 'Sự kiện',
        'Registrations': 'Đăng ký',
        'Attendance': 'Điểm danh',
        'Notifications': 'Thông báo',
        'Audit Log': 'Nhật ký hệ thống',
        'System': 'Hệ thống',
        'Users': 'Người dùng',
        'Settings': 'Cài đặt',
        'Language': 'Ngôn ngữ',
        'Navigation': 'Điều hướng',
        'Good morning': 'Chào buổi sáng',
        'Good afternoon': 'Chào buổi chiều',
        'Good evening': 'Chào buổi tối'
    },
    'zh': {
        'Dashboard': '仪表板',
        'Clubs': '俱乐部',
        'Events': '事件',
        'Registrations': '注册',
        'Attendance': '出勤',
        'Notifications': '通知',
        'Audit Log': '审计日志',
        'System': '系统',
        'Users': '用户',
        'Settings': '设置',
        'Language': '语言',
        'Navigation': '导航'
    }
};

function applyI18n() {
    const role = getRole();
    const lang = localStorage.getItem(`clubhub_lang_${role}`) || 'vi';
    if (lang === 'en') return; 
    
    const dict = translations[lang];
    if(!dict) return;
    const u = getUser();
    
    document.querySelectorAll('.sidebar-menu a').forEach(a => {
        const parts = a.innerHTML.split(' ');
        if(parts.length > 1) {
            const text = parts.slice(1).join(' ').trim();
            if(dict[text]) {
                a.innerHTML = parts[0] + ' ' + dict[text];
            }
        }
        if(u && u.role === 'organizer') {
            document.querySelectorAll('.sidebar-menu a[href*="users.html"], .sidebar-menu a[href*="settings.html"], .sidebar-menu a[href*="language.html"]').forEach(e => e.style.display = 'none');
            document.querySelectorAll('.sidebar-menu-title').forEach(e => { if(e.innerText === 'System') e.style.display = 'none'; });
        }
    });


    
    document.querySelectorAll('.sidebar-menu-title').forEach(el => {
        const text = el.innerText.trim();
        if(dict[text]) el.innerText = dict[text];
    });
    
    // Translate common form elements, headers and buttons
    const translateNode = (node) => {
        if (node.nodeType === Node.TEXT_NODE) {
            const text = node.nodeValue.trim();
            if (text && dict[text]) {
                node.nodeValue = node.nodeValue.replace(text, dict[text]);
            }
        } else if (node.nodeType === Node.ELEMENT_NODE) {
            if (node.tagName !== 'SCRIPT' && node.tagName !== 'STYLE') {
                node.childNodes.forEach(translateNode);
            }
        }
    };
    
    // Translate the entire body
    document.body.childNodes.forEach(translateNode);
    
    // Translate placeholders
    document.querySelectorAll('input[placeholder]').forEach(input => {
        const text = input.getAttribute('placeholder');
        if (text && dict[text]) {
            input.setAttribute('placeholder', dict[text]);
        }
    });
}

// Auto-inject Logout button to new Sidebar UI
const logoutObserver = new MutationObserver(() => {
    const sidebarUser = document.getElementById('sidebarUser');
    if (sidebarUser && sidebarUser.innerHTML.trim() !== '' && !document.getElementById('globalLogoutBtn')) {
        const btn = document.createElement('button');
        btn.id = 'globalLogoutBtn';
        btn.innerHTML = '🚪';
        btn.style.cssText = 'margin-left:auto; background:none; border:none; cursor:pointer; font-size:20px; transition: transform 0.2s;';
        btn.onmouseover = () => btn.style.transform = 'scale(1.1)';
        btn.onmouseout = () => btn.style.transform = 'scale(1)';
        btn.onclick = logout;
        btn.title = 'Đăng xuất';
        sidebarUser.appendChild(btn);
    }
});
logoutObserver.observe(document.documentElement, { childList: true, subtree: true });
