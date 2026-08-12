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
    </div><div>
        ${u?`<span>Chao, ${u.full_name} (${u.role})</span> <a href="#" onclick="logout()">Dang xuat</a>`:'<a href="/views/auth/login.html">Dang nhap</a>'}
    </div>`;
    document.body.prepend(nav);
}
async function apiCall(endpoint,method='GET',data=null) {
    const opts={method,headers:{'Content-Type':'application/json'}};
    if(data) opts.body=JSON.stringify(data);
    const res=await fetch(API_BASE+endpoint,opts);
    const json=await res.json();
    if(json.status==='error') throw new Error(json.message);
    return json;
}
