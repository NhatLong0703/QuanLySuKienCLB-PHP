import { useState } from 'react'
import {
  AreaChart, Area, BarChart, Bar, PieChart, Pie, Cell,
  XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend,
} from 'recharts'

type Language = 'en' | 'vi' | 'zh'
type NavSection = 'dashboard' | 'clubs' | 'events' | 'registrations' | 'attendance' | 'users' | 'notifications' | 'audit' | 'settings'
type ListingType = 'club' | 'event'
type MgmtStatus = 'active' | 'draft' | 'closed'

interface ManagedListing {
  id: number; type: ListingType; title: string; category: string
  date: string; location: string; totalSpots: number; booked: number
  status: MgmtStatus; price: number; image: string
}

// ── Static data ──────────────────────────────────────────────────────────────

const LISTINGS: ManagedListing[] = [
  { id: 1, type: 'event', title: 'Startup Pitch Night', category: 'Networking', date: 'Aug 22, 2026', location: 'Innovation Hub, Floor 3', totalSpots: 60, booked: 46, status: 'active', price: 0, image: 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=80&h=80&fit=crop&auto=format' },
  { id: 2, type: 'club', title: 'Photography Society', category: 'Arts', date: 'Every Saturday', location: 'Studio B', totalSpots: 20, booked: 17, status: 'active', price: 15, image: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=80&h=80&fit=crop&auto=format' },
  { id: 3, type: 'event', title: 'Jazz Under the Stars', category: 'Music', date: 'Aug 29, 2026', location: 'Rooftop Garden', totalSpots: 80, booked: 80, status: 'closed', price: 25, image: 'https://images.unsplash.com/photo-1511192336575-5a79af67a629?w=80&h=80&fit=crop&auto=format' },
  { id: 4, type: 'club', title: 'Urban Runners Club', category: 'Sports', date: 'Tue & Thu', location: 'Central Park', totalSpots: 50, booked: 28, status: 'active', price: 0, image: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=80&h=80&fit=crop&auto=format' },
  { id: 5, type: 'event', title: 'Winter Gala 2026', category: 'Social', date: 'Dec 12, 2026', location: 'Grand Ballroom', totalSpots: 200, booked: 0, status: 'draft', price: 60, image: 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=80&h=80&fit=crop&auto=format' },
  { id: 6, type: 'club', title: 'Book & Philosophy Circle', category: 'Learning', date: 'Every Wednesday', location: 'Library Room', totalSpots: 18, booked: 13, status: 'active', price: 0, image: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=80&h=80&fit=crop&auto=format' },
]

const BOOKINGS_TREND = [
  { week: 'Jul 7',  bookings: 12, revenue: 180 },
  { week: 'Jul 14', bookings: 19, revenue: 340 },
  { week: 'Jul 21', bookings: 15, revenue: 225 },
  { week: 'Jul 28', bookings: 28, revenue: 520 },
  { week: 'Aug 4',  bookings: 34, revenue: 610 },
  { week: 'Aug 11', bookings: 41, revenue: 890 },
  { week: 'Aug 18', bookings: 38, revenue: 760 },
  { week: 'Aug 25', bookings: 47, revenue: 1040 },
]

const CATEGORY_BOOKINGS = [
  { category: 'Networking', bookings: 46 },
  { category: 'Arts',       bookings: 17 },
  { category: 'Music',      bookings: 80 },
  { category: 'Sports',     bookings: 28 },
  { category: 'Learning',   bookings: 13 },
  { category: 'Social',     bookings: 0  },
]

const TYPE_SPLIT = [
  { name: 'Events', value: 126 },
  { name: 'Clubs',  value: 58  },
]

const REGISTRATIONS = [
  { id: 'REG-0001', member: 'Liam Okafor',    initials: 'LO', color: '#c07a3a', event: 'Startup Pitch Night',    status: 'confirmed',  date: 'Aug 10' },
  { id: 'REG-0002', member: 'Marcus Denton',   initials: 'MD', color: '#2a5fa8', event: 'Photography Society',   status: 'confirmed',  date: 'Aug 9'  },
  { id: 'REG-0003', member: 'Yuna Park',       initials: 'YP', color: '#2a8a5a', event: 'Jazz Under the Stars',  status: 'waitlisted', date: 'Aug 8'  },
  { id: 'REG-0004', member: 'Sofia Reyes',     initials: 'SR', color: '#8a2aa8', event: 'Urban Runners Club',    status: 'confirmed',  date: 'Aug 7'  },
  { id: 'REG-0005', member: 'James Whitfield', initials: 'JW', color: '#a83a2a', event: 'Startup Pitch Night',   status: 'cancelled',  date: 'Aug 6'  },
]

const ATTENDANCE = [
  { regId: 'REG-0007', member: 'Liam Okafor',  initials: 'LO', color: '#c07a3a', event: 'Summer Jazz Concert',    status: 'Present', time: 'Aug 8, 05:58 PM', by: 'Kai',   note: '—' },
  { regId: 'REG-0008', member: 'Marcus Denton', initials: 'MD', color: '#2a5fa8', event: 'Summer Jazz Concert',    status: 'Present', time: 'Aug 8, 06:03 PM', by: 'Kai',   note: 'Late arrival, seated separately' },
  { regId: 'REG-0009', member: 'Yuna Park',     initials: 'YP', color: '#2a8a5a', event: 'Arduino Intro Workshop', status: 'Present', time: 'Aug 5, 10:05 AM', by: 'Sofia', note: '—' },
]

const USERS = [
  { id: 1, name: 'Liam Okafor',    initials: 'LO', color: '#c07a3a', role: 'Member',        email: 'liam.okafor@email.com',    joined: 'Jan 12, 2026', bookings: 4, online: true,  lastSeen: 'Now',       activity: 'Viewing Startup Pitch Night' },
  { id: 2, name: 'Marcus Denton',  initials: 'MD', color: '#2a5fa8', role: 'Member',        email: 'marcus.d@email.com',       joined: 'Feb 3, 2026',  bookings: 2, online: true,  lastSeen: 'Now',       activity: 'Browsing Events' },
  { id: 3, name: 'Yuna Park',      initials: 'YP', color: '#2a8a5a', role: 'Member',        email: 'yuna.park@email.com',      joined: 'Mar 18, 2026', bookings: 3, online: true,  lastSeen: 'Now',       activity: 'Booking Photography Society' },
  { id: 4, name: 'Sofia Reyes',    initials: 'SR', color: '#8a2aa8', role: 'Member',        email: 'sofia.r@email.com',        joined: 'Mar 25, 2026', bookings: 1, online: false, lastSeen: '14 min ago', activity: 'Checked registrations' },
  { id: 5, name: 'James Whitfield',initials: 'JW', color: '#a83a2a', role: 'Member',        email: 'j.whitfield@email.com',    joined: 'Apr 7, 2026',  bookings: 1, online: false, lastSeen: '1 hr ago',  activity: 'Cancelled REG-0005' },
  { id: 6, name: 'Priya Nair',     initials: 'PN', color: '#0369a1', role: 'Club Leader',   email: 'priya.n@email.com',        joined: 'Feb 14, 2026', bookings: 5, online: true,  lastSeen: 'Now',       activity: 'Managing Urban Runners Club' },
  { id: 7, name: 'Oliver Grant',   initials: 'OG', color: '#15803d', role: 'Club Leader',   email: 'oliver.g@email.com',       joined: 'Jan 28, 2026', bookings: 6, online: false, lastSeen: '3 hr ago',  activity: 'Updated club description' },
  { id: 8, name: 'Amara Osei',     initials: 'AO', color: '#b45309', role: 'Administrator', email: 'amara.osei@clubhub.com',   joined: 'Jan 1, 2026',  bookings: 0, online: true,  lastSeen: 'Now',       activity: 'Manager Dashboard' },
]

// ── Palette ───────────────────────────────────────────────────────────────────
// Validated: #e07a2a (orange), #6d28d9 (violet), #0369a1 (blue), #15803d (green)
const C = { orange: '#e07a2a', violet: '#6d28d9', blue: '#0369a1', green: '#15803d' }

const CATEGORIES_LIST = ['Networking', 'Arts', 'Music', 'Sports', 'Food & Drink', 'Learning', 'Social', 'Technology']
const EMPTY_FORM = { type: 'event' as ListingType, title: '', category: 'Networking', date: '', location: '', totalSpots: 30, price: 0, status: 'draft' as MgmtStatus }

const statusBadge: Record<MgmtStatus, { bg: string; color: string }> = {
  active: { bg: '#dcfce7', color: '#15803d' },
  draft:  { bg: '#fef9c3', color: '#a16207' },
  closed: { bg: '#fee2e2', color: '#b91c1c' },
}
const regStatusBadge: Record<string, { bg: string; color: string }> = {
  confirmed:  { bg: '#dcfce7', color: '#15803d' },
  waitlisted: { bg: '#fef9c3', color: '#a16207' },
  cancelled:  { bg: '#fee2e2', color: '#b91c1c' },
}

const NAV_ITEMS: { key: NavSection; label: string; icon: string }[] = [
  { key: 'dashboard',     label: 'Dashboard',     icon: '◎' },
  { key: 'clubs',         label: 'Clubs',         icon: '◆' },
  { key: 'events',        label: 'Events',        icon: '◎' },
  { key: 'registrations', label: 'Registrations', icon: '▣' },
  { key: 'attendance',    label: 'Attendance',    icon: '✓' },
  { key: 'users',         label: 'Users',         icon: '◎' },
  { key: 'notifications', label: 'Notifications', icon: '◎' },
  { key: 'audit',         label: 'Audit Log',     icon: '≡' },
  { key: 'settings',      label: 'Settings',      icon: '⚙' },
]

const sectionMeta: Record<NavSection, { title: string; sub: string }> = {
  dashboard:     { title: 'Dashboard',     sub: 'Overview of all clubs and events' },
  clubs:         { title: 'Clubs',         sub: 'Manage all active and draft clubs' },
  events:        { title: 'Events',        sub: 'Manage all scheduled events' },
  registrations: { title: 'Registrations', sub: 'Member sign-ups and waitlist' },
  attendance:    { title: 'Attendance',    sub: 'Check-in records for completed events' },
  users:         { title: 'Users',         sub: 'Active members and session activity' },
  notifications: { title: 'Notifications', sub: 'Alerts and system messages' },
  audit:         { title: 'Audit Log',     sub: 'Record of all manager actions' },
  settings:      { title: 'Settings',      sub: 'Manage your account and preferences' },
}

// ── Translations ──────────────────────────────────────────────────────────────
const translations: Record<Language, Record<string, string>> = {
  en: {
    language: 'Language',
    selectLanguage: 'Select Language',
    english: 'English',
    vietnamese: 'Tiếng Việt',
    chinese: '中文',
    languageDescription: 'Choose your preferred language for the interface',
    accountSettings: 'Account Settings',
    notifications: 'Notifications',
    appearance: 'Appearance',
    privacy: 'Privacy & Security',
    emailNotifications: 'Email Notifications',
    enableNotifications: 'Enable email notifications for important updates',
    darkMode: 'Dark Mode',
    enableDarkMode: 'Use dark theme for better visibility in low-light environments',
    twoFactorAuth: 'Two-Factor Authentication',
    enableTwoFactor: 'Add an extra layer of security to your account',
    save: 'Save Changes',
    settings: 'Settings',
  },
  vi: {
    language: 'Ngôn Ngữ',
    selectLanguage: 'Chọn Ngôn Ngữ',
    english: 'English',
    vietnamese: 'Tiếng Việt',
    chinese: '中文',
    languageDescription: 'Chọn ngôn ngữ ưa thích của bạn cho giao diện',
    accountSettings: 'Cài Đặt Tài Khoản',
    notifications: 'Thông Báo',
    appearance: 'Giao Diện',
    privacy: 'Quyền Riêng Tư & Bảo Mật',
    emailNotifications: 'Thông Báo Qua Email',
    enableNotifications: 'Bật thông báo email cho các bản cập nhật quan trọng',
    darkMode: 'Chế Độ Tối',
    enableDarkMode: 'Sử dụng chủ đề tối để dễ nhìn hơn trong môi trường có ánh sáng yếu',
    twoFactorAuth: 'Xác Thực Hai Yếu Tố',
    enableTwoFactor: 'Thêm một lớp bảo mật bổ sung cho tài khoản của bạn',
    save: 'Lưu Thay Đổi',
    settings: 'Cài Đặt',
  },
  zh: {
    language: '语言',
    selectLanguage: '选择语言',
    english: 'English',
    vietnamese: 'Tiếng Việt',
    chinese: '中文',
    languageDescription: '为界面选择您首选的语言',
    accountSettings: '账户设置',
    notifications: '通知',
    appearance: '外观',
    privacy: '隐私和安全',
    emailNotifications: '电子邮件通知',
    enableNotifications: '启用重要更新的电子邮件通知',
    darkMode: '深色模式',
    enableDarkMode: '在低光环境中使用深色主题以获得更好的可见性',
    twoFactorAuth: '双因素认证',
    enableTwoFactor: '为您的账户添加额外的安全保护层',
    save: '保存更改',
    settings: '设置',
  },
}

// ── Shared tooltip style ──────────────────────────────────────────────────────
const tooltipStyle = {
  backgroundColor: '#0d0f1a',
  border: '1px solid #2a2d40',
  borderRadius: 8,
  padding: '8px 14px',
  fontFamily: "'DM Sans', sans-serif",
  fontSize: 13,
  color: '#e8e8f0',
}

// ── Component ─────────────────────────────────────────────────────────────────
export default function ManagerPage() {
  const [nav, setNav] = useState<NavSection>('dashboard')
  const [language, setLanguage] = useState<Language>('en')
  const [listings, setListings] = useState<ManagedListing[]>(LISTINGS)
  const [showForm, setShowForm] = useState(false)
  const [editing, setEditing] = useState<ManagedListing | null>(null)
  const [form, setForm] = useState(EMPTY_FORM)
  const [deleteConfirm, setDeleteConfirm] = useState<number | null>(null)
  const [userFilter, setUserFilter] = useState<'all' | 'online'>('all')
  const [emailNotifications, setEmailNotifications] = useState(true)
  const [darkMode, setDarkMode] = useState(false)
  const [twoFactorAuth, setTwoFactorAuth] = useState(false)

  const t = translations[language]

  const openCreate = () => { setEditing(null); setForm(EMPTY_FORM); setShowForm(true) }
  const openEdit = (l: ManagedListing) => {
    setEditing(l)
    setForm({ type: l.type, title: l.title, category: l.category, date: l.date, location: l.location, totalSpots: l.totalSpots, price: l.price, status: l.status })
    setShowForm(true)
  }
  const saveForm = () => {
    if (!form.title.trim() || !form.date.trim() || !form.location.trim()) return
    if (editing) {
      setListings(p => p.map(l => l.id === editing.id ? { ...l, ...form } : l))
    } else {
      setListings(p => [{ id: Date.now(), ...form, booked: 0, image: 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=80&h=80&fit=crop&auto=format' }, ...p])
    }
    setShowForm(false)
  }

  const cycleStatus = (id: number) =>
    setListings(p => p.map(l => l.id !== id ? l : { ...l, status: l.status === 'draft' ? 'active' : l.status === 'active' ? 'closed' : 'draft' }))

  const stats = {
    total: listings.length,
    active: listings.filter(l => l.status === 'active').length,
    booked: listings.reduce((a, l) => a + l.booked, 0),
    revenue: listings.reduce((a, l) => a + l.booked * l.price, 0),
  }

  const onlineCount = USERS.filter(u => u.online).length
  const visibleUsers = userFilter === 'online' ? USERS.filter(u => u.online) : USERS

  const inp: React.CSSProperties = {
    width: '100%', padding: '9px 13px',
    backgroundColor: '#fff', border: '1px solid #ddd8ce',
    borderRadius: 8, color: '#1a1a1a', fontSize: 14,
    fontFamily: "'DM Sans', sans-serif", outline: 'none', boxSizing: 'border-box',
  }

  return (
    <div style={{ display: 'flex', minHeight: 'calc(100vh - 37px)', backgroundColor: '#f0ebe0' }}>

      {/* ── Sidebar ── */}
      <aside style={{ width: 228, flexShrink: 0, backgroundColor: '#0d0f1a', display: 'flex', flexDirection: 'column', position: 'sticky', top: 37, height: 'calc(100vh - 37px)' }}>
        <div style={{ padding: '28px 24px 22px' }}>
          <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 22, color: '#fff', lineHeight: 1.1 }}>ClubHub</div>
          <div style={{ fontSize: 10, letterSpacing: '0.12em', color: '#6b7280', marginTop: 3, textTransform: 'uppercase' }}>Management System</div>
        </div>
        <nav style={{ flex: 1, padding: '0 12px', overflowY: 'auto' }}>
          <div style={{ fontSize: 10, letterSpacing: '0.12em', color: '#4b5563', padding: '0 12px', marginBottom: 10, textTransform: 'uppercase' }}>Navigation</div>
          {NAV_ITEMS.map(item => {
            const active = nav === item.key
            return (
              <button key={item.key} onClick={() => setNav(item.key)} style={{
                display: 'flex', alignItems: 'center', gap: 10, width: '100%',
                padding: '9px 14px', borderRadius: 8, border: 'none', cursor: 'pointer',
                marginBottom: 2, backgroundColor: active ? '#e07a2a' : 'transparent',
                color: active ? '#fff' : '#9ca3af', fontFamily: "'DM Sans', sans-serif",
                fontSize: 14, fontWeight: active ? 600 : 400, transition: 'all 0.15s', textAlign: 'left', position: 'relative',
              }}
                onMouseEnter={e => { if (!active) e.currentTarget.style.backgroundColor = 'rgba(255,255,255,0.05)' }}
                onMouseLeave={e => { if (!active) e.currentTarget.style.backgroundColor = 'transparent' }}
              >
                <span style={{ fontSize: 12, opacity: 0.7 }}>{item.icon}</span>
                {item.label}
                {item.key === 'users' && (
                  <span style={{ marginLeft: 'auto', backgroundColor: active ? 'rgba(255,255,255,0.25)' : '#1e3a5f', color: active ? '#fff' : '#60a5fa', fontSize: 10, fontWeight: 700, padding: '2px 7px', borderRadius: 10 }}>{onlineCount}</span>
                )}
                {active && item.key !== 'users' && <span style={{ marginLeft: 'auto', width: 6, height: 6, borderRadius: '50%', backgroundColor: '#fff', opacity: 0.7 }} />}
              </button>
            )
          })}
        </nav>
        <div style={{ padding: '16px 24px', borderTop: '1px solid #1e2030', display: 'flex', alignItems: 'center', gap: 10 }}>
          <div style={{ width: 34, height: 34, borderRadius: '50%', backgroundColor: '#b45309', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 13, fontWeight: 700, color: '#fff', flexShrink: 0 }}>AO</div>
          <div>
            <div style={{ fontSize: 13, fontWeight: 600, color: '#fff' }}>Amara Osei</div>
            <div style={{ fontSize: 11, color: '#6b7280', textTransform: 'uppercase', letterSpacing: '0.06em' }}>Administrator</div>
          </div>
        </div>
      </aside>

      {/* ── Main ── */}
      <main style={{ flex: 1, overflowY: 'auto', padding: '36px 40px' }}>
        <div style={{ marginBottom: 28 }}>
          <h1 style={{ fontFamily: "'DM Serif Display', serif", fontSize: 38, margin: 0, color: '#1a1a1a', letterSpacing: '-0.01em' }}>{sectionMeta[nav].title}</h1>
          <p style={{ margin: '6px 0 0', color: '#7a7060', fontSize: 14 }}>{sectionMeta[nav].sub}</p>
        </div>

        {/* ══ DASHBOARD ══ */}
        {nav === 'dashboard' && (
          <>
            {/* Stat cards */}
            <div style={{ display: 'grid', gridTemplateColumns: '1.6fr 1fr 1fr', gap: 14, marginBottom: 28 }}>
              <div style={{ backgroundColor: '#0d0f1a', borderRadius: 14, padding: '28px 28px 24px', color: '#fff' }}>
                <div style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#6b7280', marginBottom: 16 }}>Total Bookings</div>
                <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 56, lineHeight: 1 }}>{stats.booked}</div>
              </div>
              <div style={{ backgroundColor: '#fff', borderRadius: 14, padding: '28px 24px', border: '1px solid #e5e0d5' }}>
                <div style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#7a7060', marginBottom: 16 }}>Active Listings</div>
                <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 56, lineHeight: 1, color: '#1a1a1a' }}>{stats.active}</div>
              </div>
              <div style={{ backgroundColor: '#fff', borderRadius: 14, padding: '28px 24px', border: '1px solid #e5e0d5' }}>
                <div style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#7a7060', marginBottom: 16 }}>Revenue</div>
                <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 42, lineHeight: 1, color: '#1a1a1a' }}>${stats.revenue.toLocaleString()}</div>
                <div style={{ fontSize: 12, color: '#7a7060', marginTop: 8 }}>from paid listings</div>
              </div>
            </div>

            {/* ── Charts row 1: Bookings trend + Type split ── */}
            <div style={{ display: 'grid', gridTemplateColumns: '2fr 1fr', gap: 16, marginBottom: 16 }}>
              {/* Bookings over time */}
              <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', padding: '22px 24px' }}>
                <div style={{ fontWeight: 600, fontSize: 15, marginBottom: 4 }}>Bookings Over Time</div>
                <div style={{ fontSize: 12, color: '#7a7060', marginBottom: 18 }}>Weekly new bookings — last 8 weeks</div>
                <ResponsiveContainer width="100%" height={200}>
                  <AreaChart data={BOOKINGS_TREND} margin={{ top: 4, right: 4, left: -18, bottom: 0 }}>
                    <defs>
                      <linearGradient id="bookingFill" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="5%" stopColor={C.orange} stopOpacity={0.18} />
                        <stop offset="95%" stopColor={C.orange} stopOpacity={0} />
                      </linearGradient>
                    </defs>
                    <CartesianGrid strokeDasharray="3 3" stroke="#f0ebe0" vertical={false} />
                    <XAxis dataKey="week" tick={{ fontSize: 11, fill: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} axisLine={false} tickLine={false} />
                    <YAxis tick={{ fontSize: 11, fill: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} axisLine={false} tickLine={false} />
                    <Tooltip contentStyle={tooltipStyle} cursor={{ stroke: '#e5e0d5', strokeWidth: 1 }} />
                    <Area type="monotone" dataKey="bookings" stroke={C.orange} strokeWidth={2} fill="url(#bookingFill)" dot={false} activeDot={{ r: 5, fill: C.orange, stroke: '#fff', strokeWidth: 2 }} name="Bookings" />
                  </AreaChart>
                </ResponsiveContainer>
              </div>

              {/* Type split donut */}
              <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', padding: '22px 24px' }}>
                <div style={{ fontWeight: 600, fontSize: 15, marginBottom: 4 }}>Booking Split</div>
                <div style={{ fontSize: 12, color: '#7a7060', marginBottom: 8 }}>Events vs Clubs</div>
                <ResponsiveContainer width="100%" height={200}>
                  <PieChart>
                    <Pie data={TYPE_SPLIT} cx="50%" cy="50%" innerRadius={54} outerRadius={80} paddingAngle={3} dataKey="value" label={({ name, percent }) => `${name} ${Math.round(percent * 100)}%`} labelLine={false}>
                      <Cell fill={C.orange} />
                      <Cell fill={C.violet} />
                    </Pie>
                    <Tooltip contentStyle={tooltipStyle} />
                  </PieChart>
                </ResponsiveContainer>
                <div style={{ display: 'flex', justifyContent: 'center', gap: 20, marginTop: 4 }}>
                  {TYPE_SPLIT.map((t, i) => (
                    <div key={t.name} style={{ display: 'flex', alignItems: 'center', gap: 6, fontSize: 12, color: '#7a7060' }}>
                      <span style={{ width: 10, height: 10, borderRadius: 2, backgroundColor: i === 0 ? C.orange : C.violet, flexShrink: 0 }} />
                      {t.name}
                    </div>
                  ))}
                </div>
              </div>
            </div>

            {/* ── Charts row 2: Category bookings + Revenue trend ── */}
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 28 }}>
              {/* Bookings by category */}
              <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', padding: '22px 24px' }}>
                <div style={{ fontWeight: 600, fontSize: 15, marginBottom: 4 }}>Bookings by Category</div>
                <div style={{ fontSize: 12, color: '#7a7060', marginBottom: 18 }}>Total confirmed bookings per category</div>
                <ResponsiveContainer width="100%" height={200}>
                  <BarChart data={CATEGORY_BOOKINGS} margin={{ top: 4, right: 4, left: -18, bottom: 0 }} barCategoryGap="30%">
                    <CartesianGrid strokeDasharray="3 3" stroke="#f0ebe0" vertical={false} />
                    <XAxis dataKey="category" tick={{ fontSize: 10, fill: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} axisLine={false} tickLine={false} />
                    <YAxis tick={{ fontSize: 11, fill: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} axisLine={false} tickLine={false} />
                    <Tooltip contentStyle={tooltipStyle} cursor={{ fill: '#f0ebe080' }} />
                    <Bar dataKey="bookings" fill={C.orange} radius={[4, 4, 0, 0]} name="Bookings" label={{ position: 'top', fontSize: 11, fill: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} />
                  </BarChart>
                </ResponsiveContainer>
              </div>

              {/* Revenue trend */}
              <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', padding: '22px 24px' }}>
                <div style={{ fontWeight: 600, fontSize: 15, marginBottom: 4 }}>Revenue Trend</div>
                <div style={{ fontSize: 12, color: '#7a7060', marginBottom: 18 }}>Weekly revenue from paid events — last 8 weeks</div>
                <ResponsiveContainer width="100%" height={200}>
                  <AreaChart data={BOOKINGS_TREND} margin={{ top: 4, right: 4, left: -8, bottom: 0 }}>
                    <defs>
                      <linearGradient id="revFill" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="5%" stopColor={C.violet} stopOpacity={0.18} />
                        <stop offset="95%" stopColor={C.violet} stopOpacity={0} />
                      </linearGradient>
                    </defs>
                    <CartesianGrid strokeDasharray="3 3" stroke="#f0ebe0" vertical={false} />
                    <XAxis dataKey="week" tick={{ fontSize: 11, fill: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} axisLine={false} tickLine={false} />
                    <YAxis tick={{ fontSize: 11, fill: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} axisLine={false} tickLine={false} tickFormatter={v => `$${v}`} />
                    <Tooltip contentStyle={tooltipStyle} cursor={{ stroke: '#e5e0d5', strokeWidth: 1 }} formatter={(v: number) => [`$${v}`, 'Revenue']} />
                    <Area type="monotone" dataKey="revenue" stroke={C.violet} strokeWidth={2} fill="url(#revFill)" dot={false} activeDot={{ r: 5, fill: C.violet, stroke: '#fff', strokeWidth: 2 }} name="Revenue" />
                  </AreaChart>
                </ResponsiveContainer>
              </div>
            </div>

            {/* Listings table */}
            <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', overflow: 'hidden' }}>
              <div style={{ padding: '18px 24px 14px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid #e5e0d5' }}>
                <span style={{ fontWeight: 600, fontSize: 15 }}>All Listings</span>
                <button onClick={openCreate} style={{ padding: '8px 18px', borderRadius: 8, border: 'none', cursor: 'pointer', backgroundColor: '#e07a2a', color: '#fff', fontFamily: "'DM Sans', sans-serif", fontWeight: 600, fontSize: 13 }}>+ Create New</button>
              </div>
              <ListingsTable listings={listings} onEdit={openEdit} onDelete={setDeleteConfirm} onCycle={cycleStatus} />
            </div>
          </>
        )}

        {/* ══ CLUBS ══ */}
        {nav === 'clubs' && (
          <>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 24 }}>
              <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', padding: '22px 24px' }}>
                <div style={{ fontWeight: 600, fontSize: 15, marginBottom: 4 }}>Club Membership Fill</div>
                <div style={{ fontSize: 12, color: '#7a7060', marginBottom: 18 }}>Booked vs total spots per club</div>
                <ResponsiveContainer width="100%" height={200}>
                  <BarChart data={listings.filter(l => l.type === 'club').map(l => ({ name: l.title.split(' ').slice(0, 2).join(' '), booked: l.booked, available: l.totalSpots - l.booked }))} margin={{ top: 4, right: 4, left: -18, bottom: 0 }} barCategoryGap="28%">
                    <CartesianGrid strokeDasharray="3 3" stroke="#f0ebe0" vertical={false} />
                    <XAxis dataKey="name" tick={{ fontSize: 10, fill: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} axisLine={false} tickLine={false} />
                    <YAxis tick={{ fontSize: 11, fill: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} axisLine={false} tickLine={false} />
                    <Tooltip contentStyle={tooltipStyle} cursor={{ fill: '#f0ebe080' }} />
                    <Legend iconType="square" iconSize={10} wrapperStyle={{ fontSize: 12, color: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} />
                    <Bar dataKey="booked" fill={C.violet} radius={[4, 4, 0, 0]} name="Booked" stackId="a" />
                    <Bar dataKey="available" fill="#e5e0d5" radius={[4, 4, 0, 0]} name="Available" stackId="a" />
                  </BarChart>
                </ResponsiveContainer>
              </div>
              <div style={{ backgroundColor: '#0d0f1a', borderRadius: 14, padding: '28px 28px 24px', color: '#fff' }}>
                <div style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#6b7280', marginBottom: 16 }}>Club Members</div>
                <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 56, lineHeight: 1 }}>{listings.filter(l => l.type === 'club').reduce((a, l) => a + l.booked, 0)}</div>
                <div style={{ fontSize: 13, color: '#6b7280', marginTop: 12 }}>across {listings.filter(l => l.type === 'club').length} clubs</div>
              </div>
            </div>
            <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', overflow: 'hidden' }}>
              <div style={{ padding: '18px 24px 14px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid #e5e0d5' }}>
                <span style={{ fontWeight: 600, fontSize: 15 }}>All Clubs</span>
                <button onClick={openCreate} style={{ padding: '8px 18px', borderRadius: 8, border: 'none', cursor: 'pointer', backgroundColor: '#e07a2a', color: '#fff', fontFamily: "'DM Sans', sans-serif", fontWeight: 600, fontSize: 13 }}>+ New Club</button>
              </div>
              <ListingsTable listings={listings.filter(l => l.type === 'club')} onEdit={openEdit} onDelete={setDeleteConfirm} onCycle={cycleStatus} />
            </div>
          </>
        )}

        {/* ══ EVENTS ══ */}
        {nav === 'events' && (
          <>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 24 }}>
              <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', padding: '22px 24px' }}>
                <div style={{ fontWeight: 600, fontSize: 15, marginBottom: 4 }}>Event Capacity Utilisation</div>
                <div style={{ fontSize: 12, color: '#7a7060', marginBottom: 18 }}>Booked vs available per event</div>
                <ResponsiveContainer width="100%" height={200}>
                  <BarChart data={listings.filter(l => l.type === 'event').map(l => ({ name: l.title.split(' ').slice(0, 2).join(' '), booked: l.booked, available: l.totalSpots - l.booked }))} margin={{ top: 4, right: 4, left: -18, bottom: 0 }} barCategoryGap="28%">
                    <CartesianGrid strokeDasharray="3 3" stroke="#f0ebe0" vertical={false} />
                    <XAxis dataKey="name" tick={{ fontSize: 10, fill: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} axisLine={false} tickLine={false} />
                    <YAxis tick={{ fontSize: 11, fill: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} axisLine={false} tickLine={false} />
                    <Tooltip contentStyle={tooltipStyle} cursor={{ fill: '#f0ebe080' }} />
                    <Legend iconType="square" iconSize={10} wrapperStyle={{ fontSize: 12, color: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} />
                    <Bar dataKey="booked" fill={C.orange} radius={[4, 4, 0, 0]} name="Booked" stackId="a" />
                    <Bar dataKey="available" fill="#e5e0d5" radius={[4, 4, 0, 0]} name="Available" stackId="a" />
                  </BarChart>
                </ResponsiveContainer>
              </div>
              <div style={{ backgroundColor: '#0d0f1a', borderRadius: 14, padding: '28px 28px 24px', color: '#fff' }}>
                <div style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#6b7280', marginBottom: 16 }}>Event Attendees</div>
                <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 56, lineHeight: 1 }}>{listings.filter(l => l.type === 'event').reduce((a, l) => a + l.booked, 0)}</div>
                <div style={{ fontSize: 13, color: '#6b7280', marginTop: 12 }}>across {listings.filter(l => l.type === 'event').length} events</div>
              </div>
            </div>
            <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', overflow: 'hidden' }}>
              <div style={{ padding: '18px 24px 14px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid #e5e0d5' }}>
                <span style={{ fontWeight: 600, fontSize: 15 }}>All Events</span>
                <button onClick={openCreate} style={{ padding: '8px 18px', borderRadius: 8, border: 'none', cursor: 'pointer', backgroundColor: '#e07a2a', color: '#fff', fontFamily: "'DM Sans', sans-serif", fontWeight: 600, fontSize: 13 }}>+ New Event</button>
              </div>
              <ListingsTable listings={listings.filter(l => l.type === 'event')} onEdit={openEdit} onDelete={setDeleteConfirm} onCycle={cycleStatus} />
            </div>
          </>
        )}

        {/* ══ REGISTRATIONS ══ */}
        {nav === 'registrations' && (
          <>
            <div style={{ display: 'grid', gridTemplateColumns: '1.6fr 1fr 1fr', gap: 14, marginBottom: 28 }}>
              <div style={{ backgroundColor: '#0d0f1a', borderRadius: 14, padding: '28px 28px 24px', color: '#fff' }}>
                <div style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#6b7280', marginBottom: 16 }}>Total Registrations</div>
                <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 56, lineHeight: 1 }}>{REGISTRATIONS.length}</div>
              </div>
              <div style={{ backgroundColor: '#fff', borderRadius: 14, padding: '28px 24px', border: '1px solid #e5e0d5' }}>
                <div style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#7a7060', marginBottom: 16 }}>Confirmed</div>
                <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 56, lineHeight: 1 }}>{REGISTRATIONS.filter(r => r.status === 'confirmed').length}</div>
              </div>
              <div style={{ backgroundColor: '#fff', borderRadius: 14, padding: '28px 24px', border: '1px solid #e5e0d5' }}>
                <div style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#7a7060', marginBottom: 16 }}>Waitlisted</div>
                <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 56, lineHeight: 1 }}>{REGISTRATIONS.filter(r => r.status === 'waitlisted').length}</div>
              </div>
            </div>
            <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', overflow: 'hidden' }}>
              <TableHeader cols={['REG ID', 'MEMBER', 'EVENT', 'STATUS', 'DATE']} widths="130px 1fr 1fr 140px 100px" />
              {REGISTRATIONS.map((r, i) => (
                <div key={r.id} style={{ display: 'grid', gridTemplateColumns: '130px 1fr 1fr 140px 100px', padding: '14px 24px', borderBottom: i < REGISTRATIONS.length - 1 ? '1px solid #f0ebe0' : 'none', alignItems: 'center' }}>
                  <span style={{ fontSize: 12, color: '#7a7060', fontFamily: 'monospace' }}>{r.id}</span>
                  <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <Avatar initials={r.initials} color={r.color} />
                    <span style={{ fontSize: 14, fontWeight: 500 }}>{r.member}</span>
                  </div>
                  <span style={{ fontSize: 14, color: '#3a3028' }}>{r.event}</span>
                  <StatusPill label={r.status.charAt(0).toUpperCase() + r.status.slice(1)} bg={regStatusBadge[r.status].bg} color={regStatusBadge[r.status].color} />
                  <span style={{ fontSize: 13, color: '#7a7060' }}>{r.date}</span>
                </div>
              ))}
            </div>
          </>
        )}

        {/* ══ ATTENDANCE ══ */}
        {nav === 'attendance' && (
          <>
            <div style={{ display: 'grid', gridTemplateColumns: '1.6fr 1fr 1fr', gap: 14, marginBottom: 28 }}>
              <div style={{ backgroundColor: '#0d0f1a', borderRadius: 14, padding: '28px 28px 24px', color: '#fff' }}>
                <div style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#6b7280', marginBottom: 16 }}>Total Check-ins</div>
                <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 56, lineHeight: 1 }}>{ATTENDANCE.length}</div>
              </div>
              <div style={{ backgroundColor: '#fff', borderRadius: 14, padding: '28px 24px', border: '1px solid #e5e0d5' }}>
                <div style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#7a7060', marginBottom: 16 }}>Completed Events</div>
                <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 56, lineHeight: 1 }}>2</div>
              </div>
              <div style={{ backgroundColor: '#fff', borderRadius: 14, padding: '28px 24px', border: '1px solid #e5e0d5' }}>
                <div style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#7a7060', marginBottom: 16 }}>Check-in Rate</div>
                <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 48, lineHeight: 1 }}>33%</div>
                <div style={{ fontSize: 12, color: '#7a7060', marginTop: 8 }}>of confirmed registrations</div>
              </div>
            </div>
            <div style={{ display: 'flex', alignItems: 'center', gap: 14, marginBottom: 18 }}>
              <span style={{ fontSize: 11, letterSpacing: '0.1em', textTransform: 'uppercase', color: '#7a7060' }}>Filter Event</span>
              <select style={{ padding: '8px 14px', borderRadius: 8, border: '1px solid #ddd8ce', backgroundColor: '#fff', fontSize: 13, fontFamily: "'DM Sans', sans-serif", color: '#1a1a1a', outline: 'none' }}>
                <option>All completed events</option>
                <option>Summer Jazz Concert</option>
                <option>Arduino Intro Workshop</option>
              </select>
            </div>
            <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', overflow: 'hidden' }}>
              <TableHeader cols={['REG ID', 'MEMBER', 'EVENT', 'CHECK-IN', 'TIME', 'BY', 'NOTE']} widths="110px 1fr 1fr 110px 160px 80px 1fr" />
              {ATTENDANCE.map((a, i) => (
                <div key={a.regId} style={{ display: 'grid', gridTemplateColumns: '110px 1fr 1fr 110px 160px 80px 1fr', padding: '14px 24px', borderBottom: i < ATTENDANCE.length - 1 ? '1px solid #f0ebe0' : 'none', alignItems: 'center' }}>
                  <span style={{ fontSize: 12, color: '#7a7060', fontFamily: 'monospace' }}>{a.regId}</span>
                  <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}><Avatar initials={a.initials} color={a.color} /><span style={{ fontSize: 14, fontWeight: 500 }}>{a.member}</span></div>
                  <span style={{ fontSize: 14, color: '#3a3028' }}>{a.event}</span>
                  <span style={{ display: 'inline-flex', alignItems: 'center', gap: 5, fontSize: 13, fontWeight: 600, color: '#15803d' }}><span style={{ width: 7, height: 7, borderRadius: '50%', backgroundColor: '#22c55e' }} />{a.status}</span>
                  <span style={{ fontSize: 13, color: '#3a3028' }}>{a.time}</span>
                  <span style={{ fontSize: 13, color: '#3a3028' }}>{a.by}</span>
                  <span style={{ fontSize: 13, color: '#9a9080', fontStyle: a.note === '—' ? 'normal' : 'italic' }}>{a.note}</span>
                </div>
              ))}
            </div>
          </>
        )}

        {/* ══ USERS ══ */}
        {nav === 'users' && (
          <>
            {/* Stat row */}
            <div style={{ display: 'grid', gridTemplateColumns: '1.6fr 1fr 1fr', gap: 14, marginBottom: 28 }}>
              <div style={{ backgroundColor: '#0d0f1a', borderRadius: 14, padding: '28px 28px 24px', color: '#fff' }}>
                <div style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#6b7280', marginBottom: 16 }}>Total Members</div>
                <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 56, lineHeight: 1 }}>{USERS.length}</div>
                <div style={{ marginTop: 16, display: 'flex', gap: 8 }}>
                  {USERS.filter(u => u.online).map(u => (
                    <div key={u.id} title={u.name} style={{ width: 28, height: 28, borderRadius: '50%', backgroundColor: u.color, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 10, fontWeight: 700, color: '#fff', border: '2px solid #22c55e' }}>{u.initials}</div>
                  ))}
                </div>
              </div>
              <div style={{ backgroundColor: '#fff', borderRadius: 14, padding: '28px 24px', border: '1px solid #e5e0d5' }}>
                <div style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#7a7060', marginBottom: 16 }}>Online Now</div>
                <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 56, lineHeight: 1, color: '#15803d' }}>{onlineCount}</div>
              </div>
              <div style={{ backgroundColor: '#fff', borderRadius: 14, padding: '28px 24px', border: '1px solid #e5e0d5' }}>
                <div style={{ fontSize: 10, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#7a7060', marginBottom: 16 }}>Avg Bookings</div>
                <div style={{ fontFamily: "'DM Serif Display', serif", fontSize: 56, lineHeight: 1 }}>
                  {(USERS.filter(u => u.role === 'Member').reduce((a, u) => a + u.bookings, 0) / USERS.filter(u => u.role === 'Member').length).toFixed(1)}
                </div>
                <div style={{ fontSize: 12, color: '#7a7060', marginTop: 8 }}>per member</div>
              </div>
            </div>

            {/* Bookings per user bar chart */}
            <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', padding: '22px 24px', marginBottom: 20 }}>
              <div style={{ fontWeight: 600, fontSize: 15, marginBottom: 4 }}>Bookings per Member</div>
              <div style={{ fontSize: 12, color: '#7a7060', marginBottom: 18 }}>Total confirmed bookings by user</div>
              <ResponsiveContainer width="100%" height={180}>
                <BarChart data={USERS.map(u => ({ name: u.name.split(' ')[0], bookings: u.bookings, color: u.color }))} margin={{ top: 4, right: 4, left: -18, bottom: 0 }} barCategoryGap="32%">
                  <CartesianGrid strokeDasharray="3 3" stroke="#f0ebe0" vertical={false} />
                  <XAxis dataKey="name" tick={{ fontSize: 11, fill: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} axisLine={false} tickLine={false} />
                  <YAxis tick={{ fontSize: 11, fill: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} axisLine={false} tickLine={false} />
                  <Tooltip contentStyle={tooltipStyle} cursor={{ fill: '#f0ebe080' }} />
                  <Bar dataKey="bookings" radius={[4, 4, 0, 0]} name="Bookings" fill={C.blue} label={{ position: 'top', fontSize: 11, fill: '#7a7060', fontFamily: "'DM Sans',sans-serif" }} />
                </BarChart>
              </ResponsiveContainer>
            </div>

            {/* Filter tabs */}
            <div style={{ display: 'flex', gap: 8, marginBottom: 16 }}>
              {(['all', 'online'] as const).map(f => (
                <button key={f} onClick={() => setUserFilter(f)} style={{
                  padding: '7px 16px', borderRadius: 8, border: '1px solid',
                  cursor: 'pointer', fontFamily: "'DM Sans', sans-serif", fontSize: 13, fontWeight: 500,
                  borderColor: userFilter === f ? '#e07a2a' : '#ddd8ce',
                  backgroundColor: userFilter === f ? '#fff7f0' : '#fff',
                  color: userFilter === f ? '#e07a2a' : '#7a7060',
                }}>
                  {f === 'all' ? `All Users (${USERS.length})` : `🟢 Online Now (${onlineCount})`}
                </button>
              ))}
            </div>

            {/* Users table */}
            <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', overflow: 'hidden' }}>
              <TableHeader cols={['MEMBER', 'ROLE', 'EMAIL', 'BOOKINGS', 'STATUS', 'LAST ACTIVITY']} widths="1.4fr 110px 1.4fr 90px 130px 1.6fr" />
              {visibleUsers.map((u, i) => (
                <div key={u.id} style={{ display: 'grid', gridTemplateColumns: '1.4fr 110px 1.4fr 90px 130px 1.6fr', padding: '14px 24px', borderBottom: i < visibleUsers.length - 1 ? '1px solid #f0ebe0' : 'none', alignItems: 'center', transition: 'background 0.12s' }}
                  onMouseEnter={e => (e.currentTarget.style.backgroundColor = '#faf8f4')}
                  onMouseLeave={e => (e.currentTarget.style.backgroundColor = 'transparent')}
                >
                  <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <div style={{ position: 'relative', flexShrink: 0 }}>
                      <Avatar initials={u.initials} color={u.color} size={36} />
                      {u.online && <span style={{ position: 'absolute', bottom: 0, right: 0, width: 9, height: 9, borderRadius: '50%', backgroundColor: '#22c55e', border: '2px solid #fff' }} />}
                    </div>
                    <div>
                      <div style={{ fontSize: 14, fontWeight: 600 }}>{u.name}</div>
                      <div style={{ fontSize: 11, color: '#7a7060' }}>Joined {u.joined}</div>
                    </div>
                  </div>
                  <span style={{
                    fontSize: 11, fontWeight: 600, padding: '4px 10px', borderRadius: 20, width: 'fit-content',
                    backgroundColor: u.role === 'Administrator' ? '#fef9c3' : u.role === 'Club Leader' ? '#ede9fe' : '#f0f9ff',
                    color: u.role === 'Administrator' ? '#a16207' : u.role === 'Club Leader' ? '#6d28d9' : '#0369a1',
                  }}>{u.role}</span>
                  <span style={{ fontSize: 13, color: '#7a7060' }}>{u.email}</span>
                  <div style={{ fontSize: 14, fontWeight: 600, textAlign: 'center' }}>{u.bookings}</div>
                  <div style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
                    <span style={{ width: 8, height: 8, borderRadius: '50%', backgroundColor: u.online ? '#22c55e' : '#d1d5db', flexShrink: 0 }} />
                    <span style={{ fontSize: 13, color: u.online ? '#15803d' : '#7a7060' }}>{u.online ? 'Online' : u.lastSeen}</span>
                  </div>
                  <span style={{ fontSize: 12, color: '#7a7060', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{u.activity}</span>
                </div>
              ))}
            </div>
          </>
        )}

        {/* ══ NOTIFICATIONS ══ */}
        {nav === 'notifications' && (
          <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', overflow: 'hidden' }}>
            {[
              { icon: '🎟', title: 'Jazz Under the Stars is now full', sub: '80 / 80 spots taken · Aug 8', color: '#fee2e2' },
              { icon: '📋', title: 'Winter Gala 2026 is still in draft', sub: 'Publish before Dec 1 to allow bookings · Aug 7', color: '#fef9c3' },
              { icon: '✅', title: '3 new registrations for Startup Pitch Night', sub: 'Total: 46 / 60 · Aug 6', color: '#dcfce7' },
            ].map((n, i, arr) => (
              <div key={i} style={{ display: 'flex', alignItems: 'flex-start', gap: 14, padding: '18px 24px', borderBottom: i < arr.length - 1 ? '1px solid #f0ebe0' : 'none' }}>
                <div style={{ width: 38, height: 38, borderRadius: 10, backgroundColor: n.color, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 18, flexShrink: 0 }}>{n.icon}</div>
                <div>
                  <div style={{ fontWeight: 600, fontSize: 14 }}>{n.title}</div>
                  <div style={{ fontSize: 12, color: '#7a7060', marginTop: 3 }}>{n.sub}</div>
                </div>
              </div>
            ))}
          </div>
        )}

        {/* ══ AUDIT LOG ══ */}
        {nav === 'audit' && (
          <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', overflow: 'hidden' }}>
            <TableHeader cols={['TIME', 'ACTION', 'ACTOR', 'DETAIL']} widths="160px 160px 140px 1fr" />
            {[
              { time: 'Aug 10, 09:14', action: 'Listing Updated', actor: 'Amara Osei', detail: 'Changed status of "Startup Pitch Night" to Active' },
              { time: 'Aug 9, 16:32',  action: 'Listing Created', actor: 'Amara Osei', detail: 'Created new event "Winter Gala 2026"' },
              { time: 'Aug 8, 11:05',  action: 'Booking Cancelled', actor: 'System',   detail: 'REG-0005 cancelled by member James Whitfield' },
              { time: 'Aug 7, 08:50',  action: 'Listing Closed',  actor: 'Amara Osei', detail: 'Closed "Jazz Under the Stars" — capacity reached' },
            ].map((row, i, arr) => (
              <div key={i} style={{ display: 'grid', gridTemplateColumns: '160px 160px 140px 1fr', padding: '14px 24px', borderBottom: i < arr.length - 1 ? '1px solid #f0ebe0' : 'none', alignItems: 'center' }}>
                <span style={{ fontSize: 12, color: '#7a7060', fontFamily: 'monospace' }}>{row.time}</span>
                <span style={{ fontSize: 13, fontWeight: 600 }}>{row.action}</span>
                <span style={{ fontSize: 13, color: '#3a3028' }}>{row.actor}</span>
                <span style={{ fontSize: 13, color: '#7a7060' }}>{row.detail}</span>
              </div>
            ))}
          </div>
        )}

        {/* ══ SETTINGS ══ */}
        {nav === 'settings' && (
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20, maxWidth: 1200 }}>
            {/* Language Selection Card */}
            <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', padding: '28px 24px' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 24 }}>
                <div style={{ fontSize: 20 }}>🌐</div>
                <div>
                  <h3 style={{ fontSize: 16, fontWeight: 600, margin: 0, color: '#1a1a1a' }}>{t.language}</h3>
                  <p style={{ fontSize: 13, color: '#7a7060', margin: '4px 0 0' }}>{t.languageDescription}</p>
                </div>
              </div>
              <div style={{ display: 'grid', gridTemplateColumns: '1fr', gap: 10 }}>
                {(['en', 'vi', 'zh'] as Language[]).map(lang => {
                  const langLabels = { en: t.english, vi: t.vietnamese, zh: t.chinese }
                  return (
                    <button key={lang} onClick={() => setLanguage(lang)} style={{
                      padding: '12px 16px', borderRadius: 10, border: language === lang ? '2px solid #e07a2a' : '1px solid #e5e0d5',
                      backgroundColor: language === lang ? '#fff7f0' : '#fff', cursor: 'pointer',
                      display: 'flex', alignItems: 'center', gap: 10, transition: 'all 0.2s',
                      fontFamily: "'DM Sans', sans-serif", fontSize: 14, fontWeight: language === lang ? 600 : 500,
                      color: language === lang ? '#e07a2a' : '#1a1a1a',
                    }}
                      onMouseEnter={e => { if (language !== lang) e.currentTarget.style.backgroundColor = '#faf8f4' }}
                      onMouseLeave={e => { if (language !== lang) e.currentTarget.style.backgroundColor = '#fff' }}
                    >
                      <span style={{ fontSize: 20 }}>{lang === 'en' ? '🇬🇧' : lang === 'vi' ? '🇻🇳' : '🇨🇳'}</span>
                      <div style={{ textAlign: 'left' }}>
                        <div>{langLabels[lang]}</div>
                        <div style={{ fontSize: 11, color: language === lang ? '#c07a2a' : '#7a7060', marginTop: 1 }}>
                          {language === lang ? '✓ Selected' : 'Click to select'}
                        </div>
                      </div>
                      {language === lang && <span style={{ marginLeft: 'auto', fontSize: 18 }}>✓</span>}
                    </button>
                  )
                })}
              </div>
            </div>

            {/* Notification Settings */}
            <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', padding: '28px 24px' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 24 }}>
                <div style={{ fontSize: 20 }}>🔔</div>
                <div>
                  <h3 style={{ fontSize: 16, fontWeight: 600, margin: 0, color: '#1a1a1a' }}>{t.notifications}</h3>
                  <p style={{ fontSize: 13, color: '#7a7060', margin: '4px 0 0' }}>Manage notification preferences</p>
                </div>
              </div>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                <SettingToggle label={t.emailNotifications} description={t.enableNotifications} checked={emailNotifications} onChange={setEmailNotifications} />
              </div>
            </div>

            {/* Appearance Settings */}
            <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', padding: '28px 24px' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 24 }}>
                <div style={{ fontSize: 20 }}>🎨</div>
                <div>
                  <h3 style={{ fontSize: 16, fontWeight: 600, margin: 0, color: '#1a1a1a' }}>{t.appearance}</h3>
                  <p style={{ fontSize: 13, color: '#7a7060', margin: '4px 0 0' }}>Customize your interface</p>
                </div>
              </div>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                <SettingToggle label={t.darkMode} description={t.enableDarkMode} checked={darkMode} onChange={setDarkMode} />
              </div>
            </div>

            {/* Privacy & Security Settings */}
            <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', padding: '28px 24px' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 24 }}>
                <div style={{ fontSize: 20 }}>🔒</div>
                <div>
                  <h3 style={{ fontSize: 16, fontWeight: 600, margin: 0, color: '#1a1a1a' }}>{t.privacy}</h3>
                  <p style={{ fontSize: 13, color: '#7a7060', margin: '4px 0 0' }}>Secure your account</p>
                </div>
              </div>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                <SettingToggle label={t.twoFactorAuth} description={t.enableTwoFactor} checked={twoFactorAuth} onChange={setTwoFactorAuth} />
              </div>
            </div>

            {/* Account Settings */}
            <div style={{ backgroundColor: '#fff', borderRadius: 14, border: '1px solid #e5e0d5', padding: '28px 24px' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 24 }}>
                <div style={{ fontSize: 20 }}>👤</div>
                <div>
                  <h3 style={{ fontSize: 16, fontWeight: 600, margin: 0, color: '#1a1a1a' }}>{t.accountSettings}</h3>
                  <p style={{ fontSize: 13, color: '#7a7060', margin: '4px 0 0' }}>Manage your profile</p>
                </div>
              </div>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                <div style={{ padding: '12px', backgroundColor: '#faf8f4', borderRadius: 10, borderLeft: '4px solid #e07a2a' }}>
                  <div style={{ fontSize: 13, fontWeight: 500, color: '#1a1a1a' }}>Amara Osei</div>
                  <div style={{ fontSize: 12, color: '#7a7060', marginTop: 2 }}>amara.osei@clubhub.com</div>
                  <div style={{ fontSize: 11, color: '#7a7060', marginTop: 4 }}>Member since Jan 1, 2026</div>
                </div>
              </div>
            </div>

            {/* Save Button */}
            <div style={{ gridColumn: '1 / -1' }}>
              <button style={{
                padding: '12px 28px', borderRadius: 10, border: 'none', cursor: 'pointer',
                backgroundColor: '#e07a2a', color: '#fff', fontFamily: "'DM Sans', sans-serif",
                fontWeight: 600, fontSize: 14, transition: 'all 0.2s'
              }}
                onMouseEnter={e => (e.currentTarget.style.backgroundColor = '#d06820')}
                onMouseLeave={e => (e.currentTarget.style.backgroundColor = '#e07a2a')}
              >
                {t.save}
              </button>
            </div>
          </div>
        )}
      </main>

      {/* ── Create / Edit Modal ── */}
      {showForm && (
        <div onClick={() => setShowForm(false)} style={{ position: 'fixed', inset: 0, backgroundColor: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 200, padding: 24 }}>
          <div onClick={e => e.stopPropagation()} style={{ backgroundColor: '#fff', borderRadius: 18, padding: 32, maxWidth: 500, width: '100%', maxHeight: '90vh', overflowY: 'auto', boxShadow: '0 20px 60px rgba(0,0,0,0.2)' }}>
            <h2 style={{ fontFamily: "'DM Serif Display', serif", fontSize: 26, margin: '0 0 24px', color: '#1a1a1a' }}>{editing ? 'Edit Listing' : 'New Listing'}</h2>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
              <div style={{ display: 'flex', gap: 8 }}>
                {(['event', 'club'] as ListingType[]).map(t => (
                  <button key={t} onClick={() => setForm(f => ({ ...f, type: t }))} style={{ flex: 1, padding: '9px 0', borderRadius: 8, border: `1px solid ${form.type === t ? '#e07a2a' : '#ddd8ce'}`, cursor: 'pointer', fontFamily: "'DM Sans', sans-serif", fontWeight: 600, fontSize: 14, backgroundColor: form.type === t ? '#fff7f0' : '#fff', color: form.type === t ? '#e07a2a' : '#7a7060' }}>{t === 'event' ? '🎟 Event' : '🏛 Club'}</button>
                ))}
              </div>
              {[{ label: 'TITLE *', key: 'title', ph: 'e.g. Design Thinking Workshop' }, { label: 'DATE / SCHEDULE *', key: 'date', ph: 'e.g. Sep 15, 2026 · 6:00 PM' }, { label: 'LOCATION *', key: 'location', ph: 'e.g. Main Hall, Building A' }].map(({ label, key, ph }) => (
                <div key={key}>
                  <label style={{ fontSize: 11, color: '#7a7060', letterSpacing: '0.08em', display: 'block', marginBottom: 6 }}>{label}</label>
                  <input value={(form as Record<string, unknown>)[key] as string} onChange={e => setForm(f => ({ ...f, [key]: e.target.value }))} placeholder={ph} style={{ width: '100%', padding: '9px 13px', backgroundColor: '#fff', border: '1px solid #ddd8ce', borderRadius: 8, color: '#1a1a1a', fontSize: 14, fontFamily: "'DM Sans', sans-serif", outline: 'none', boxSizing: 'border-box' }} />
                </div>
              ))}
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                <div>
                  <label style={{ fontSize: 11, color: '#7a7060', letterSpacing: '0.08em', display: 'block', marginBottom: 6 }}>CATEGORY</label>
                  <select value={form.category} onChange={e => setForm(f => ({ ...f, category: e.target.value }))} style={{ width: '100%', padding: '9px 13px', backgroundColor: '#fff', border: '1px solid #ddd8ce', borderRadius: 8, color: '#1a1a1a', fontSize: 14, fontFamily: "'DM Sans', sans-serif", outline: 'none', cursor: 'pointer' }}>{CATEGORIES_LIST.map(c => <option key={c}>{c}</option>)}</select>
                </div>
                <div>
                  <label style={{ fontSize: 11, color: '#7a7060', letterSpacing: '0.08em', display: 'block', marginBottom: 6 }}>STATUS</label>
                  <select value={form.status} onChange={e => setForm(f => ({ ...f, status: e.target.value as MgmtStatus }))} style={{ width: '100%', padding: '9px 13px', backgroundColor: '#fff', border: '1px solid #ddd8ce', borderRadius: 8, color: '#1a1a1a', fontSize: 14, fontFamily: "'DM Sans', sans-serif", outline: 'none', cursor: 'pointer' }}>
                    <option value="draft">Draft</option><option value="active">Active</option><option value="closed">Closed</option>
                  </select>
                </div>
                <div>
                  <label style={{ fontSize: 11, color: '#7a7060', letterSpacing: '0.08em', display: 'block', marginBottom: 6 }}>SPOTS</label>
                  <input type="number" min={1} value={form.totalSpots} onChange={e => setForm(f => ({ ...f, totalSpots: Number(e.target.value) }))} style={{ width: '100%', padding: '9px 13px', backgroundColor: '#fff', border: '1px solid #ddd8ce', borderRadius: 8, color: '#1a1a1a', fontSize: 14, fontFamily: "'DM Sans', sans-serif", outline: 'none', boxSizing: 'border-box' }} />
                </div>
                <div>
                  <label style={{ fontSize: 11, color: '#7a7060', letterSpacing: '0.08em', display: 'block', marginBottom: 6 }}>PRICE ($)</label>
                  <input type="number" min={0} value={form.price} onChange={e => setForm(f => ({ ...f, price: Number(e.target.value) }))} style={{ width: '100%', padding: '9px 13px', backgroundColor: '#fff', border: '1px solid #ddd8ce', borderRadius: 8, color: '#1a1a1a', fontSize: 14, fontFamily: "'DM Sans', sans-serif", outline: 'none', boxSizing: 'border-box' }} />
                </div>
              </div>
            </div>
            <div style={{ display: 'flex', gap: 10, marginTop: 24 }}>
              <button onClick={() => setShowForm(false)} style={{ flex: 1, padding: '11px 0', borderRadius: 9, border: '1px solid #ddd8ce', backgroundColor: 'transparent', color: '#7a7060', cursor: 'pointer', fontFamily: "'DM Sans', sans-serif", fontWeight: 600 }}>Cancel</button>
              <button onClick={saveForm} disabled={!form.title.trim() || !form.date.trim() || !form.location.trim()} style={{ flex: 2, padding: '11px 0', borderRadius: 9, border: 'none', backgroundColor: '#e07a2a', color: '#fff', cursor: 'pointer', fontFamily: "'DM Sans', sans-serif", fontWeight: 700, fontSize: 15, opacity: (!form.title.trim() || !form.date.trim() || !form.location.trim()) ? 0.4 : 1 }}>
                {editing ? 'Save Changes' : 'Create Listing'}
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ── Delete confirm ── */}
      {deleteConfirm !== null && (
        <div onClick={() => setDeleteConfirm(null)} style={{ position: 'fixed', inset: 0, backgroundColor: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 200 }}>
          <div onClick={e => e.stopPropagation()} style={{ backgroundColor: '#fff', borderRadius: 16, padding: 28, maxWidth: 360, width: '100%', margin: '0 24px', boxShadow: '0 20px 60px rgba(0,0,0,0.2)' }}>
            <h3 style={{ fontFamily: "'DM Serif Display', serif", fontSize: 22, margin: '0 0 8px', color: '#1a1a1a' }}>Delete listing?</h3>
            <p style={{ color: '#7a7060', fontSize: 14, margin: '0 0 22px', lineHeight: 1.5 }}>"{listings.find(l => l.id === deleteConfirm)?.title}" will be permanently removed.</p>
            <div style={{ display: 'flex', gap: 10 }}>
              <button onClick={() => setDeleteConfirm(null)} style={{ flex: 1, padding: '11px 0', borderRadius: 9, border: '1px solid #ddd8ce', backgroundColor: 'transparent', color: '#7a7060', cursor: 'pointer', fontFamily: "'DM Sans', sans-serif", fontWeight: 600 }}>Cancel</button>
              <button onClick={() => { setListings(p => p.filter(l => l.id !== deleteConfirm)); setDeleteConfirm(null) }} style={{ flex: 1, padding: '11px 0', borderRadius: 9, border: 'none', backgroundColor: '#ef4444', color: '#fff', cursor: 'pointer', fontFamily: "'DM Sans', sans-serif", fontWeight: 700 }}>Delete</button>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}

// ── Sub-components ────────────────────────────────────────────────────────────

function Avatar({ initials, color, size = 32 }: { initials: string; color: string; size?: number }) {
  return (
    <div style={{ width: size, height: size, borderRadius: '50%', backgroundColor: color, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: size * 0.35, fontWeight: 700, color: '#fff', flexShrink: 0 }}>
      {initials}
    </div>
  )
}

function SettingToggle({ label, description, checked, onChange }: { label: string; description: string; checked: boolean; onChange: (value: boolean) => void }) {
  return (
    <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', paddingBottom: 12, borderBottom: '1px solid #f0ebe0' }}>
      <div>
        <div style={{ fontSize: 14, fontWeight: 500, color: '#1a1a1a' }}>{label}</div>
        <div style={{ fontSize: 12, color: '#7a7060', marginTop: 4 }}>{description}</div>
      </div>
      <button onClick={() => onChange(!checked)} style={{
        width: 48, height: 28, borderRadius: 14, border: 'none', cursor: 'pointer',
        backgroundColor: checked ? '#e07a2a' : '#ddd8ce', position: 'relative',
        transition: 'all 0.3s', flexShrink: 0, marginTop: 2,
      }}>
        <div style={{
          position: 'absolute', width: 24, height: 24, borderRadius: '50%', backgroundColor: '#fff',
          top: 2, left: checked ? 22 : 2, transition: 'all 0.3s', boxShadow: '0 2px 4px rgba(0,0,0,0.1)'
        }} />
      </button>
    </div>
  )
}

function StatusPill({ label, bg, color }: { label: string; bg: string; color: string }) {
  return (
    <span style={{ display: 'inline-flex', alignItems: 'center', gap: 5, padding: '4px 10px', borderRadius: 20, fontSize: 12, fontWeight: 600, backgroundColor: bg, color, width: 'fit-content' }}>
      <span style={{ width: 6, height: 6, borderRadius: '50%', backgroundColor: color }} />
      {label}
    </span>
  )
}

function TableHeader({ cols, widths }: { cols: string[]; widths: string }) {
  return (
    <div style={{ display: 'grid', gridTemplateColumns: widths, padding: '10px 24px', borderBottom: '1px solid #e5e0d5', backgroundColor: '#faf8f4' }}>
      {cols.map(c => <span key={c} style={{ fontSize: 11, letterSpacing: '0.08em', color: '#7a7060', textTransform: 'uppercase', fontWeight: 600 }}>{c}</span>)}
    </div>
  )
}

function ListingsTable({ listings, onEdit, onDelete, onCycle }: { listings: ManagedListing[]; onEdit: (l: ManagedListing) => void; onDelete: (id: number) => void; onCycle: (id: number) => void }) {
  return (
    <>
      <div style={{ display: 'grid', gridTemplateColumns: '2fr 80px 120px 90px 130px 110px', padding: '10px 24px', borderBottom: '1px solid #e5e0d5', backgroundColor: '#faf8f4' }}>
        {['TITLE', 'TYPE', 'BOOKINGS', 'REVENUE', 'STATUS', 'ACTIONS'].map(c => (
          <span key={c} style={{ fontSize: 11, letterSpacing: '0.08em', color: '#7a7060', textTransform: 'uppercase', fontWeight: 600, textAlign: c === 'ACTIONS' ? 'right' : 'left' }}>{c}</span>
        ))}
      </div>
      {listings.length === 0 && <div style={{ padding: '32px 24px', textAlign: 'center', color: '#7a7060', fontSize: 14 }}>No listings.</div>}
      {listings.map((l, i) => {
        const pct = Math.round((l.booked / l.totalSpots) * 100)
        return (
          <div key={l.id} style={{ display: 'grid', gridTemplateColumns: '2fr 80px 120px 90px 130px 110px', padding: '14px 24px', borderBottom: i < listings.length - 1 ? '1px solid #f0ebe0' : 'none', alignItems: 'center', transition: 'background 0.12s' }}
            onMouseEnter={e => (e.currentTarget.style.backgroundColor = '#faf8f4')}
            onMouseLeave={e => (e.currentTarget.style.backgroundColor = 'transparent')}
          >
            <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
              <img src={l.image} alt={l.title} style={{ width: 40, height: 40, borderRadius: 8, objectFit: 'cover', flexShrink: 0 }} />
              <div>
                <div style={{ fontWeight: 600, fontSize: 14 }}>{l.title}</div>
                <div style={{ fontSize: 12, color: '#7a7060', marginTop: 2 }}>{l.category} · {l.date}</div>
              </div>
            </div>
            <span style={{ fontSize: 12, fontWeight: 600, padding: '4px 10px', borderRadius: 20, backgroundColor: l.type === 'club' ? '#ede9fe' : '#e0f2fe', color: l.type === 'club' ? '#6d28d9' : '#0369a1' }}>{l.type}</span>
            <div>
              <div style={{ fontSize: 13 }}>{l.booked} / {l.totalSpots}</div>
              <div style={{ height: 3, backgroundColor: '#e5e0d5', borderRadius: 2, marginTop: 5, width: 70 }}>
                <div style={{ height: '100%', width: `${pct}%`, borderRadius: 2, backgroundColor: pct >= 100 ? '#ef4444' : pct >= 80 ? '#f59e0b' : '#e07a2a' }} />
              </div>
            </div>
            <div style={{ fontSize: 14, fontWeight: 600, color: l.price === 0 ? '#7a7060' : '#15803d' }}>{l.price === 0 ? '—' : `$${(l.booked * l.price).toLocaleString()}`}</div>
            <button onClick={() => onCycle(l.id)} title="Click to cycle status" style={{ display: 'inline-flex', alignItems: 'center', gap: 5, padding: '5px 12px', borderRadius: 20, border: 'none', cursor: 'pointer', fontSize: 12, fontWeight: 600, backgroundColor: statusBadge[l.status].bg, color: statusBadge[l.status].color, width: 'fit-content' }}>
              <span style={{ width: 6, height: 6, borderRadius: '50%', backgroundColor: statusBadge[l.status].color }} />
              {l.status.charAt(0).toUpperCase() + l.status.slice(1)}
            </button>
            <div style={{ display: 'flex', gap: 6, justifyContent: 'flex-end' }}>
              <button onClick={() => onEdit(l)} style={{ padding: '5px 12px', borderRadius: 7, border: '1px solid #ddd8ce', backgroundColor: 'transparent', color: '#1a1a1a', cursor: 'pointer', fontSize: 12, fontFamily: "'DM Sans', sans-serif" }}>Edit</button>
              <button onClick={() => onDelete(l.id)} style={{ padding: '5px 9px', borderRadius: 7, border: '1px solid #fca5a5', backgroundColor: '#fee2e2', color: '#b91c1c', cursor: 'pointer', fontSize: 12 }}>🗑</button>
            </div>
          </div>
        )
      })}
    </>
  )
}
