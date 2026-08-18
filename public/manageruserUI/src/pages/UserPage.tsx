import { useState } from 'react'

type Category = 'all' | 'club' | 'event'
type Status = 'open' | 'full' | 'soon'

interface Listing {
  id: number
  type: 'club' | 'event'
  title: string
  category: string
  date: string
  location: string
  spots: number
  totalSpots: number
  status: Status
  image: string
  description: string
  host: string
  price: number
}

const listings: Listing[] = [
  { id: 1, type: 'event', title: 'Startup Pitch Night', category: 'Networking', date: 'Aug 22, 2026 · 7:00 PM', location: 'Innovation Hub, Floor 3', spots: 14, totalSpots: 60, status: 'open', image: 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&h=340&fit=crop&auto=format', description: 'Watch 10 early-stage founders pitch live. Q&A, drinks, and networking to follow.', host: 'Alex Rivera', price: 0 },
  { id: 2, type: 'club', title: 'Photography Society', category: 'Arts', date: 'Every Saturday · 10:00 AM', location: 'Studio B, Arts Building', spots: 3, totalSpots: 20, status: 'soon', image: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&h=340&fit=crop&auto=format', description: 'Weekly shoots, critiques, and darkroom sessions for all skill levels.', host: 'Maya Chen', price: 15 },
  { id: 3, type: 'event', title: 'Jazz Under the Stars', category: 'Music', date: 'Aug 29, 2026 · 8:00 PM', location: 'Rooftop Garden, East Tower', spots: 0, totalSpots: 80, status: 'full', image: 'https://images.unsplash.com/photo-1511192336575-5a79af67a629?w=600&h=340&fit=crop&auto=format', description: 'Live jazz trio with cocktails and a stunning city skyline backdrop.', host: 'Marcus Webb', price: 25 },
  { id: 4, type: 'club', title: 'Urban Runners Club', category: 'Sports', date: 'Tue & Thu · 6:30 AM', location: 'Central Park East Entrance', spots: 22, totalSpots: 50, status: 'open', image: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&h=340&fit=crop&auto=format', description: 'Casual runs from 5K to half-marathon distance. All paces welcome.', host: 'Priya Nair', price: 0 },
  { id: 5, type: 'event', title: 'Fermentation Workshop', category: 'Food & Drink', date: 'Sep 6, 2026 · 2:00 PM', location: 'Community Kitchen, Level 1', spots: 8, totalSpots: 16, status: 'open', image: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&h=340&fit=crop&auto=format', description: 'Learn to make kombucha, kimchi, and sourdough from scratch.', host: 'Lena Fischer', price: 40 },
  { id: 6, type: 'club', title: 'Book & Philosophy Circle', category: 'Learning', date: 'Every Wednesday · 6:00 PM', location: 'Library Reading Room', spots: 5, totalSpots: 18, status: 'open', image: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&h=340&fit=crop&auto=format', description: 'Monthly books, weekly ideas. We argue respectfully and drink good tea.', host: 'Oliver Grant', price: 0 },
]

const statusConfig: Record<Status, { bg: string; color: string; label: string }> = {
  open: { bg: '#dcfce7', color: '#15803d', label: 'Open' },
  full: { bg: '#fee2e2', color: '#b91c1c', label: 'Full' },
  soon: { bg: '#fef9c3', color: '#a16207', label: 'Filling Fast' },
}

export default function UserPage() {
  const [filter, setFilter] = useState<Category>('all')
  const [search, setSearch] = useState('')
  const [booked, setBooked] = useState<Set<number>>(new Set())
  const [selected, setSelected] = useState<Listing | null>(null)

  const filtered = listings.filter(l => {
    const matchType = filter === 'all' || l.type === filter
    const matchSearch = l.title.toLowerCase().includes(search.toLowerCase()) || l.category.toLowerCase().includes(search.toLowerCase())
    return matchType && matchSearch
  })

  const toggleBook = (id: number) => {
    setBooked(prev => { const n = new Set(prev); n.has(id) ? n.delete(id) : n.add(id); return n })
  }

  return (
    <div style={{ backgroundColor: '#f0ebe0', minHeight: 'calc(100vh - 37px)' }}>
      {/* Hero strip */}
      <div style={{ backgroundColor: '#0d0f1a', padding: '40px 40px 36px' }}>
        <div style={{ maxWidth: 1200, margin: '0 auto' }}>
          <div style={{ fontSize: 11, letterSpacing: '0.12em', textTransform: 'uppercase', color: '#6b7280', marginBottom: 10 }}>ClubHub · Member Portal</div>
          <h1 style={{ fontFamily: "'DM Serif Display', serif", fontSize: 48, color: '#fff', margin: 0, letterSpacing: '-0.01em', lineHeight: 1.05 }}>
            Discover Clubs & Events
          </h1>
          <p style={{ color: '#9ca3af', marginTop: 10, fontSize: 15 }}>
            Browse, filter, and book your spot — {booked.size > 0 ? `${booked.size} booking${booked.size !== 1 ? 's' : ''} active` : 'no bookings yet'}
          </p>

          {/* Search */}
          <div style={{ marginTop: 24, display: 'flex', gap: 10, flexWrap: 'wrap' }}>
            <input
              value={search}
              onChange={e => setSearch(e.target.value)}
              placeholder="Search clubs and events…"
              style={{
                flex: 1, minWidth: 240, padding: '11px 18px',
                backgroundColor: '#1e2030', border: '1px solid #2a2d40',
                borderRadius: 10, color: '#e8e8f0', fontSize: 14,
                fontFamily: "'DM Sans', sans-serif", outline: 'none',
              }}
            />
            <div style={{ display: 'flex', backgroundColor: '#1e2030', borderRadius: 10, padding: 4, gap: 2, border: '1px solid #2a2d40' }}>
              {(['all', 'club', 'event'] as Category[]).map(f => (
                <button key={f} onClick={() => setFilter(f)} style={{
                  padding: '7px 18px', borderRadius: 7, border: 'none', cursor: 'pointer',
                  fontFamily: "'DM Sans', sans-serif", fontWeight: 500, fontSize: 13, transition: 'all 0.15s',
                  backgroundColor: filter === f ? '#e07a2a' : 'transparent',
                  color: filter === f ? '#fff' : '#6b7280',
                }}>
                  {f === 'all' ? 'All' : f === 'club' ? '🏛 Clubs' : '🎟 Events'}
                </button>
              ))}
            </div>
          </div>
        </div>
      </div>

      <div style={{ maxWidth: 1200, margin: '0 auto', padding: '32px 40px' }}>
        {/* Active bookings bar */}
        {booked.size > 0 && (
          <div style={{
            marginBottom: 28, padding: '14px 20px',
            backgroundColor: '#fff7f0', border: '1px solid #f4c094',
            borderRadius: 12, display: 'flex', alignItems: 'center', gap: 12,
          }}>
            <span style={{ fontSize: 20 }}>🎫</span>
            <div>
              <div style={{ fontWeight: 600, fontSize: 14, color: '#1a1a1a' }}>Your Bookings</div>
              <div style={{ color: '#7a7060', fontSize: 13 }}>{listings.filter(l => booked.has(l.id)).map(l => l.title).join(' · ')}</div>
            </div>
          </div>
        )}

        {/* Results count */}
        <div style={{ fontSize: 13, color: '#7a7060', marginBottom: 20 }}>
          {filtered.length} result{filtered.length !== 1 ? 's' : ''}
        </div>

        {/* Cards grid */}
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(340px, 1fr))', gap: 20 }}>
          {filtered.map(listing => {
            const sc = statusConfig[listing.status]
            const isBooked = booked.has(listing.id)
            const pct = ((listing.totalSpots - listing.spots) / listing.totalSpots) * 100
            return (
              <div
                key={listing.id}
                onClick={() => setSelected(listing)}
                style={{
                  backgroundColor: '#fff', borderRadius: 14,
                  border: `1px solid ${isBooked ? '#f4c094' : '#e5e0d5'}`,
                  overflow: 'hidden', cursor: 'pointer',
                  boxShadow: isBooked ? '0 0 0 2px #f4c09460' : 'none',
                  transition: 'transform 0.15s, box-shadow 0.15s',
                }}
                onMouseEnter={e => { e.currentTarget.style.transform = 'translateY(-3px)'; e.currentTarget.style.boxShadow = isBooked ? '0 4px 20px rgba(224,122,42,0.2)' : '0 4px 20px rgba(0,0,0,0.08)' }}
                onMouseLeave={e => { e.currentTarget.style.transform = 'translateY(0)'; e.currentTarget.style.boxShadow = isBooked ? '0 0 0 2px #f4c09460' : 'none' }}
              >
                {/* Image */}
                <div style={{ position: 'relative', height: 176, backgroundColor: '#e5e0d5' }}>
                  <img src={listing.image} alt={listing.title} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                  <div style={{ position: 'absolute', inset: 0, background: 'linear-gradient(to top, rgba(0,0,0,0.3) 0%, transparent 50%)' }} />
                  <div style={{ position: 'absolute', top: 12, left: 12, display: 'flex', gap: 6 }}>
                    <span style={{ padding: '4px 10px', borderRadius: 20, fontSize: 11, fontWeight: 600, backgroundColor: listing.type === 'club' ? '#ede9fe' : '#e0f2fe', color: listing.type === 'club' ? '#6d28d9' : '#0369a1' }}>
                      {listing.type}
                    </span>
                    <span style={{ padding: '4px 10px', borderRadius: 20, fontSize: 11, fontWeight: 600, backgroundColor: sc.bg, color: sc.color }}>
                      {sc.label}
                    </span>
                  </div>
                  {isBooked && (
                    <div style={{ position: 'absolute', top: 12, right: 12, backgroundColor: '#e07a2a', borderRadius: 20, padding: '4px 11px', fontSize: 11, fontWeight: 700, color: '#fff' }}>✓ Booked</div>
                  )}
                </div>

                {/* Body */}
                <div style={{ padding: '16px 18px 18px' }}>
                  <div style={{ fontSize: 11, color: '#e07a2a', fontWeight: 600, letterSpacing: '0.06em', textTransform: 'uppercase', marginBottom: 4 }}>{listing.category}</div>
                  <h3 style={{ fontFamily: "'DM Serif Display', serif", fontSize: 20, margin: '0 0 8px', color: '#1a1a1a', lineHeight: 1.2 }}>{listing.title}</h3>
                  <p style={{ fontSize: 13, color: '#7a7060', margin: '0 0 12px', lineHeight: 1.55 }}>{listing.description}</p>

                  <div style={{ display: 'flex', flexDirection: 'column', gap: 4, marginBottom: 14 }}>
                    {[['📅', listing.date], ['📍', listing.location], ['👤', `Hosted by ${listing.host}`]].map(([icon, text]) => (
                      <div key={text as string} style={{ fontSize: 12, color: '#7a7060', display: 'flex', gap: 6 }}><span>{icon}</span><span>{text}</span></div>
                    ))}
                  </div>

                  {/* Spots bar */}
                  <div style={{ marginBottom: 14 }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: 12, color: '#7a7060', marginBottom: 5 }}>
                      <span>{listing.totalSpots - listing.spots} / {listing.totalSpots} spots taken</span>
                      <span style={{ fontWeight: 600, color: listing.price === 0 ? '#15803d' : '#1a1a1a' }}>{listing.price === 0 ? 'Free' : `$${listing.price}`}</span>
                    </div>
                    <div style={{ height: 4, backgroundColor: '#f0ebe0', borderRadius: 2 }}>
                      <div style={{ height: '100%', width: `${pct}%`, borderRadius: 2, backgroundColor: listing.status === 'full' ? '#ef4444' : listing.status === 'soon' ? '#f59e0b' : '#e07a2a', transition: 'width 0.4s' }} />
                    </div>
                  </div>

                  <button
                    onClick={e => { e.stopPropagation(); if (listing.status !== 'full' || isBooked) toggleBook(listing.id) }}
                    disabled={listing.status === 'full' && !isBooked}
                    style={{
                      width: '100%', padding: '10px 0', borderRadius: 9, border: '1px solid',
                      cursor: listing.status === 'full' && !isBooked ? 'not-allowed' : 'pointer',
                      fontFamily: "'DM Sans', sans-serif", fontWeight: 600, fontSize: 14, transition: 'all 0.15s',
                      borderColor: isBooked ? '#f4c094' : listing.status === 'full' ? '#e5e0d5' : '#e07a2a',
                      backgroundColor: isBooked ? '#fff7f0' : listing.status === 'full' ? '#f9f7f4' : '#e07a2a',
                      color: isBooked ? '#e07a2a' : listing.status === 'full' ? '#b0a898' : '#fff',
                    }}
                  >
                    {isBooked ? '✓ Cancel Booking' : listing.status === 'full' ? 'Fully Booked' : listing.type === 'club' ? 'Join Club' : 'Book Spot'}
                  </button>
                </div>
              </div>
            )
          })}
        </div>
      </div>

      {/* Detail Modal */}
      {selected && (
        <div onClick={() => setSelected(null)} style={{ position: 'fixed', inset: 0, backgroundColor: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 100, padding: 24 }}>
          <div onClick={e => e.stopPropagation()} style={{ backgroundColor: '#fff', borderRadius: 18, border: '1px solid #e5e0d5', maxWidth: 540, width: '100%', overflow: 'hidden', boxShadow: '0 20px 60px rgba(0,0,0,0.2)' }}>
            <div style={{ position: 'relative', height: 210, backgroundColor: '#e5e0d5' }}>
              <img src={selected.image} alt={selected.title} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
              <div style={{ position: 'absolute', inset: 0, background: 'linear-gradient(to top, rgba(255,255,255,0.8) 0%, transparent 60%)' }} />
              <button onClick={() => setSelected(null)} style={{ position: 'absolute', top: 14, right: 14, width: 32, height: 32, borderRadius: '50%', border: 'none', backgroundColor: 'rgba(255,255,255,0.85)', color: '#1a1a1a', cursor: 'pointer', fontSize: 18, display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: 300 }}>×</button>
            </div>
            <div style={{ padding: '20px 28px 28px' }}>
              <div style={{ fontSize: 11, color: '#e07a2a', fontWeight: 600, letterSpacing: '0.06em', textTransform: 'uppercase', marginBottom: 4 }}>{selected.category}</div>
              <h2 style={{ fontFamily: "'DM Serif Display', serif", fontSize: 28, margin: '0 0 10px', color: '#1a1a1a', lineHeight: 1.15 }}>{selected.title}</h2>
              <p style={{ color: '#7a7060', fontSize: 14, lineHeight: 1.6, margin: '0 0 20px' }}>{selected.description}</p>
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10, marginBottom: 22 }}>
                {[['📅', 'Date', selected.date], ['📍', 'Location', selected.location], ['👤', 'Host', selected.host], ['💰', 'Price', selected.price === 0 ? 'Free' : `$${selected.price}`], ['🎟', 'Spots left', `${selected.spots} of ${selected.totalSpots}`], ['📌', 'Type', selected.type.charAt(0).toUpperCase() + selected.type.slice(1)]].map(([icon, label, value]) => (
                  <div key={label as string} style={{ backgroundColor: '#faf8f4', borderRadius: 9, padding: '10px 14px', border: '1px solid #e5e0d5' }}>
                    <div style={{ fontSize: 11, color: '#7a7060', marginBottom: 2 }}>{icon} {label}</div>
                    <div style={{ fontSize: 13, fontWeight: 600, color: '#1a1a1a' }}>{value}</div>
                  </div>
                ))}
              </div>
              <button
                onClick={() => { toggleBook(selected.id); setSelected(null) }}
                disabled={selected.status === 'full' && !booked.has(selected.id)}
                style={{
                  width: '100%', padding: '13px 0', borderRadius: 10, border: '1px solid',
                  cursor: selected.status === 'full' && !booked.has(selected.id) ? 'not-allowed' : 'pointer',
                  fontFamily: "'DM Sans', sans-serif", fontWeight: 700, fontSize: 15,
                  borderColor: booked.has(selected.id) ? '#f4c094' : selected.status === 'full' ? '#e5e0d5' : '#e07a2a',
                  backgroundColor: booked.has(selected.id) ? '#fff7f0' : selected.status === 'full' ? '#f9f7f4' : '#e07a2a',
                  color: booked.has(selected.id) ? '#e07a2a' : selected.status === 'full' ? '#b0a898' : '#fff',
                }}
              >
                {booked.has(selected.id) ? '✓ Cancel Booking' : selected.status === 'full' ? 'Fully Booked' : selected.type === 'club' ? 'Join Club' : 'Book Spot'}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
