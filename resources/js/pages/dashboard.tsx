import EmailChart from '@/components/EmailChart'
import AppLayout from '@/layouts/AppLayout'
import { Link } from '@inertiajs/react'



export default function Dashboard({ emailStats, recentEmails = [], summary }) {
  return (
    <AppLayout>
      <div style={{ padding: '10px', backgroundColor: '#f2f4f8', minHeight: '100vh' }}>
        <div style={{ maxWidth: '800px', margin: '0 auto' }}>
          {/* Header Tiles: Grouped First and Second Tiles in One Row */}
          <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '10px', flexWrap: 'wrap' }}>
            {/* Group 1: Total Emails + With Attachments */}
            <div style={{ display: 'flex', gap: '10px', flexWrap: 'wrap' }}>
              <SummaryCard title="Total Emails" value={summary.items} color="#fff" icon="📧" />
              <SummaryCard title="With Attachments" value={summary.items} color="#fff" icon="📎" />
            </div>

            {/* Group 2: Items Created + Unread Emails */}
            <div style={{ display: 'flex', gap: '10px', flexWrap: 'wrap' }}>
              <SummaryCard title="Items Created" value={summary.items} color="#fff" icon="✅" />
              <SummaryCard title="Unread Emails" value={summary.unread} color="#fff" icon="📩" />
            </div>
          </div>

          {/* Main Content Grid */}
          <div style={{ display: 'grid', gridTemplateColumns: '1fr', gap: '10px' }}>
            {/* Sidebar */}
            <div style={{ backgroundColor: '#ffffff', borderRadius: '8px', padding: '12px', boxShadow: '0 1px 2px rgba(0,0,0,0.1)' }}>
              <h3 style={{ fontSize: '12px', fontWeight: 600, marginBottom: '6px' }}>Quick Stats</h3>
              <p style={{ fontSize: '10px' }}>Total Emails: {emailStats.data.reduce((a, b) => a + b, 0)}</p>
              <p style={{ fontSize: '10px' }}>Last Update: {new Date().toLocaleDateString()}</p>
            </div>

            {/* Main Panel */}
            <div>
              <div style={{ backgroundColor: '#ffffff', borderRadius: '8px', padding: '12px', marginBottom: '10px', boxShadow: '0 1px 2px rgba(0,0,0,0.1)' }}>
                <h2 style={{ fontSize: '14px', fontWeight: 600, marginBottom: '8px' }}>Email Chart Overview</h2>
                <EmailChart labels={emailStats.labels} data={emailStats.data} />
              </div>

              <div style={{ backgroundColor: '#ffffff', borderRadius: '12px', padding: '24px', boxShadow: '0 1px 3px rgba(0,0,0,0.1)' }} className="card">
  <h2 style={{ fontSize: '18px', fontWeight: 600, marginBottom: '16px' }}>Recent Emails</h2>
  <table style={{ width: '100%', borderCollapse: 'collapse' }}>
    <thead style={{ backgroundColor: '#f9fafb' }}>
      <tr>
        <th style={tableHeader}>Subject</th>
        <th style={tableHeader}>From</th>
        <th style={tableHeader}>Date</th>
        <th style={tableHeader}>Attachment</th>
        <th style={tableHeader}>Action</th>
      </tr>
    </thead>
    <tbody>
      {recentEmails.map(email => (
        <tr key={email.id}>
          <td style={tableCell}><Link href={`/emails/${email.id}`}>{email.subject}</Link></td>
          <td style={tableCell}>{email.from}</td>
          <td style={tableCell}>{new Date(email.created_at).toLocaleDateString()}</td>
          <td style={tableCell}>{JSON.parse(email.attachments).length !== 0 ? '📎 Yes' : '—'}</td>
          <td style={tableCell}>
            <Link href={`/emails/${email.id}`} style={{
              backgroundColor: '#4f46e5',
              color: '#fff',
              padding: '4px 10px',
              borderRadius: '6px',
              textDecoration: 'none',
              fontSize: '12px'
            }}>
              View
            </Link>
          </td>
        </tr>
      ))}
    </tbody>
  </table>
</div>

            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  )
}

function SummaryCard({ title, value, color, icon }) {
    return (
      <div
        style={{
          backgroundColor: color,
          color: '#1f2937',
          padding: '10px 14px',
          borderRadius: '8px',
          display: 'flex',
          alignItems: 'center',
          gap: '10px',
          boxShadow: '0 1px 2px rgba(0,0,0,0.04)',
          flex: 1,
          minWidth: '140px',
          maxWidth: '180px',
        }}
      >
        <div style={{ fontSize: '20px' }}>{icon}</div>
        <div>
          <div style={{ fontSize: '11px', opacity: 0.6, textTransform: 'uppercase' }}>{title}</div>
          <div style={{ fontSize: '20px', fontWeight: 600 }}>{value}</div>
        </div>
      </div>
    )
  }
  
  

const tableHeader = {
  textAlign: 'left',
  padding: '6px 8px',
  fontSize: '10px',
  fontWeight: 600,
  borderBottom: '1px solid #e5e7eb',
}

const tableCell = {
  padding: '6px 8px',
  borderBottom: '1px solid #f3f4f6',
  fontSize: '10px',
  color: '#374151',
}

