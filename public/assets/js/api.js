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
            const res = await apiCall('/notification/list');
            const content = document.getElementById('notiContent');
            if(!res.data.length) {
                content.innerHTML = '<small>Không có thông báo mới.</small>';
                return;
            }
            content.innerHTML = res.data.map(n => `<div style="border-bottom:1px solid #eee; padding:8px 0;">
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
