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
        ${u?'<a href="/views/member/events.html">Su kien</a>':''}
    </div><div style="display:flex; align-items:center;">
        ${u?`<a href="#" onclick="showNotifications()" style="margin-right:15px;position:relative;">&#128276;<span id="notiCount" style="display:none;position:absolute;top:-5px;right:-10px;background:red;color:white;border-radius:50%;padding:2px 5px;font-size:10px;">0</span></a>
        <a href="/views/member/profile.html" style="margin-right:15px;"><span>Chao, ${u.full_name}</span></a> <a href="#" onclick="logout()">Dang xuat</a>`:'<a href="/views/auth/login.html">Dang nhap</a>'}
    </div>`;
    document.body.prepend(nav);
    if(u) loadNotifications();
}
async function loadNotifications() {
    try {
        const res = await apiCall('/notification/my?user_id=' + getUser().id);
        if(res.data.length > 0) {
            const el = document.getElementById('notiCount');
            el.innerText = res.data.length;
            el.style.display = 'inline-block';
        }
    } catch(e) {}
}
function showNotifications() {
    let modal = document.getElementById('notiModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'notiModal';
        modal.style.cssText = 'position:fixed;top:60px;right:20px;width:320px;max-height:400px;overflow-y:auto;background:white;border:1px solid #ddd;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:9999;padding:15px;display:none;';
        document.body.appendChild(modal);
        
        // Hide modal when clicking outside
        document.addEventListener('click', (e) => {
            if(!modal.contains(e.target) && !e.target.closest('[onclick="showNotifications()"]')) {
                modal.style.display = 'none';
            }
        });
    }
    
    if (modal.style.display === 'block') {
        modal.style.display = 'none';
        return;
    }
    
    modal.style.display = 'block';
    modal.innerHTML = '<div style="text-align:center;padding:20px;">Đang tải...</div>';
    
    apiCall('/notification/my?user_id=' + getUser().id).then(res => {
        if(res.data.length === 0) {
            modal.innerHTML = '<div style="text-align:center;padding:20px;color:#666;">Không có thông báo nào</div>';
            return;
        }
        let html = '<h4 style="margin-top:0;border-bottom:1px solid #eee;padding-bottom:10px;">Thông báo của bạn</h4>';
        res.data.forEach(n => {
            html += `<div style="padding:10px 0;border-bottom:1px solid #f5f5f5;">
                <div style="font-weight:bold;font-size:14px;color:#007bff;">${n.title}</div>
                <div style="font-size:13px;color:#333;margin-top:4px;">${n.content}</div>
                <div style="font-size:11px;color:#999;margin-top:4px;">🕒 ${n.created_at}</div>
            </div>`;
        });
        modal.innerHTML = html;
        const countBadge = document.getElementById('notiCount');
        if(countBadge) countBadge.style.display = 'none'; // Ẩn số đếm sau khi đã mở xem
    }).catch(e => {
        modal.innerHTML = '<div style="text-align:center;padding:20px;color:red;">Lỗi tải thông báo</div>';
    });
}
async function apiCall(endpoint, method='GET', data=null) {
    const opts = { method, headers: {} };
    if (data) {
        if (data instanceof FormData) {
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
