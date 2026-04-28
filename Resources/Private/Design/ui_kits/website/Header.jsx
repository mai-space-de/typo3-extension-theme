function Header({ active, onNav, lang, onLang, user, onLogin }) {
  const items = [
    { id: 'home', label: 'Willkommen' },
    { id: 'angebote', label: 'Angebote' },
    { id: 'veranstaltungen', label: 'Veranstaltungen' },
    { id: 'neuigkeiten', label: 'Neuigkeiten' },
    { id: 'mitwirken', label: 'Mitwirken' },
    { id: 'verein', label: 'Über den Verein' },
  ];
  return (
    <header style={{
      background: '#fff', borderBottom: '1px solid var(--bgm-line-2)',
      position: 'sticky', top: 0, zIndex: 10,
    }}>
      <div style={{
        maxWidth: 1152, margin: '0 auto', padding: '14px 32px',
        display: 'flex', alignItems: 'center', gap: 28,
      }}>
        <a href="#" onClick={(e) => { e.preventDefault(); onNav('home'); }} style={{ display: 'flex', alignItems: 'center', gap: 12, textDecoration: 'none' }}>
          <img src="../../assets/logo-mark.png" alt="" style={{ height: 44, width: 'auto' }} />
          <div style={{ lineHeight: 1.15 }}>
            <div style={{ fontWeight: 700, color: 'var(--bgm-teal-900)', fontSize: 15 }}>Begleitung geflüchteter</div>
            <div style={{ fontWeight: 700, color: 'var(--bgm-teal-900)', fontSize: 15 }}>Menschen e.V. Pulheim</div>
          </div>
        </a>
        <nav style={{ display: 'flex', gap: 4, marginLeft: 'auto' }}>
          {items.map(it => (
            <a key={it.id} href="#" onClick={(e) => { e.preventDefault(); onNav(it.id); }}
               style={{
                 padding: '8px 12px', textDecoration: 'none',
                 fontSize: 15, fontWeight: 600,
                 color: active === it.id ? 'var(--bgm-teal-900)' : 'var(--bgm-ink-2)',
                 borderBottom: active === it.id ? '2px solid var(--bgm-gold-500)' : '2px solid transparent',
               }}>{it.label}</a>
          ))}
        </nav>
        <div style={{ display: 'flex', gap: 10, alignItems: 'center' }}>
          <select value={lang} onChange={(e) => onLang(e.target.value)} style={{
            padding: '6px 8px', border: '1px solid var(--bgm-line)', borderRadius: 4,
            fontFamily: 'inherit', fontSize: 13, background: '#fff', color: 'var(--bgm-ink-2)',
          }}>
            <option value="de">DE</option>
            <option value="en">EN</option>
            <option value="ar">AR</option>
            <option value="uk">UK</option>
          </select>
          {user ? (
            <span style={{ fontSize: 13, color: 'var(--bgm-teal-800)', fontWeight: 600 }}>
              <Icon name="user-circle" size={16} /> {user}
            </span>
          ) : (
            <Button variant="outline" onClick={onLogin}>Login</Button>
          )}
        </div>
      </div>
    </header>
  );
}
window.Header = Header;
