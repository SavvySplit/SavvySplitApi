import { useEffect, useState } from 'react'

export default function AppLayout({ children }) {
  const [dark, setDark] = useState(false)

/*   useEffect(() => {
    const saved = localStorage.getItem('theme')
    if (saved === 'dark') setDark(true)
  }, []) */

  useEffect(() => {
    document.body.className = dark ? 'dark' : ''
    localStorage.setItem('theme', dark ? 'dark' : 'light')
  }, [dark]) 

  return (
    <div>
      <button
        onClick={() => setDark(!dark)}
        style={{
          position: 'fixed',
          top: 10,
          right: 10,
          padding: '6px 10px',
          background: dark ? '#333' : '#eee',
          color: dark ? '#fff' : '#000',
          borderRadius: '5px',
          border: 'none',
          cursor: 'pointer',
          zIndex: 1000,
        }}
      >
        {dark ? '🌙 Dark Mode' : '☀️ Light Mode'}
      </button>

      <main>{children}</main>
    </div>
  )
}
