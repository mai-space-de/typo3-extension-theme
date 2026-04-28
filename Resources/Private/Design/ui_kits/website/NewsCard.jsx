function NewsCard({ tag, date, title, excerpt }) {
  return (
    <article style={{
      background: '#fff', border: '1px solid var(--bgm-line-2)',
      padding: 24, transition: 'border-color 200ms', cursor: 'pointer',
    }}
      onMouseEnter={(e) => e.currentTarget.style.borderColor = 'var(--bgm-teal-700)'}
      onMouseLeave={(e) => e.currentTarget.style.borderColor = 'var(--bgm-line-2)'}
    >
      <div style={{ display: 'flex', gap: 10, alignItems: 'center', marginBottom: 14 }}>
        <span style={{
          background: 'var(--bgm-teal-100)', color: 'var(--bgm-teal-900)',
          padding: '3px 10px', fontSize: 12, fontWeight: 600, letterSpacing: '.02em',
          borderRadius: 999,
        }}>{tag}</span>
        <span style={{ fontSize: 13, color: 'var(--bgm-ink-3)' }}>{date}</span>
      </div>
      <h3 style={{ fontSize: 19, margin: '0 0 10px', color: 'var(--bgm-teal-900)' }}>{title}</h3>
      <p style={{ fontSize: 14, lineHeight: 1.6, color: 'var(--bgm-ink-2)', margin: 0 }}>{excerpt}</p>
      <div style={{ marginTop: 16, fontSize: 14, fontWeight: 600, color: 'var(--bgm-teal-700)' }}>Weiterlesen →</div>
    </article>
  );
}
window.NewsCard = NewsCard;
