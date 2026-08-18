import { useState } from 'react'
import UserPage from './pages/UserPage'
import ManagerPage from './pages/ManagerPage'

type Role = 'user' | 'manager'

export default function App() {
  const [role, setRole] = useState<Role>('user')

  return (
    <div style={{ minHeight: '100vh', display: 'flex', flexDirection: 'column' }}>
      {/* Slim role-switcher bar */}
      <div style={{
        backgroundColor: '#0d0f1a',
        display: 'flex',
        justifyContent: 'center',
        padding: '8px 0',
        gap: 4,
      }}>
        {(['user', 'manager'] as Role[]).map(r => (
          <button
            key={r}
            onClick={() => setRole(r)}
            style={{
              padding: '5px 20px',
              borderRadius: 6,
              border: 'none',
              cursor: 'pointer',
              fontFamily: "'DM Sans', sans-serif",
              fontWeight: 500,
              fontSize: 12,
              letterSpacing: '0.05em',
              textTransform: 'uppercase',
              transition: 'all 0.18s',
              backgroundColor: role === r ? '#e07a2a' : 'transparent',
              color: role === r ? '#fff' : '#6b7280',
            }}
          >
            {r === 'user' ? 'Member View' : 'Manager View'}
          </button>
        ))}
      </div>

      <div style={{ flex: 1 }}>
        {role === 'user' ? <UserPage /> : <ManagerPage />}
      </div>
    </div>
  )
}
