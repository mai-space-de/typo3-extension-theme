function EventRow({ day, month, title, time, location, full, onAnmelden }) {
  return (
    <div style={{
      display: 'grid', gridTemplateColumns: '76px 1fr auto', gap: 24, alignItems: 'center',
      padding: '20px 24px',
      background: '#fff', borderBottom: '1px solid var(--bgm-line-2)',
    }}>
      <div style={{
        textAlign: 'center', border: '2px solid var(--bgm-teal-700)',
        padding: '8px 0', minWidth: 64,
      }}>
        <div style={{ fontSize: 24, fontWeight: 700, color: 'var(--bgm-teal-900)', lineHeight: 1 }}>{day}</div>
        <div style={{ fontSize: 11, fontWeight: 700, letterSpacing: '.08em', textTransform: 'uppercase', color: 'var(--bgm-teal-700)', marginTop: 4 }}>{month}</div>
      </div>
      <div>
        <div style={{ fontSize: 17, fontWeight: 700, color: 'var(--bgm-teal-900)', marginBottom: 4 }}>{title}</div>
        <div style={{ fontSize: 13, color: 'var(--bgm-ink-3)' }}>
          <Icon name="clock" size={13} /> {time} · <Icon name="map-pin" size={13} color="gold" /> {location}
        </div>
      </div>
      <div>
        {full
          ? <span style={{ background: '#fbe1df', color: '#7a1a16', padding: '6px 12px', borderRadius: 999, fontSize: 12, fontWeight: 600 }}>Ausgebucht</span>
          : <Button variant="outline" onClick={onAnmelden}>Anmelden</Button>}
      </div>
    </div>
  );
}
window.EventRow = EventRow;
