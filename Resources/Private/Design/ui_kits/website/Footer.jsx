function Footer() {
  const cols = [
    { t: 'Angebote', l: ['Bunter Tisch', 'Ladies Club', 'Männer International'] },
    { t: 'Mitwirken', l: ['Spenden', 'Mitarbeiter gesucht', 'Download Bereich'] },
    { t: 'Über den Verein', l: ['Aufgaben des Vereins', 'Aktive Mitglieder', 'Vernetzungen', 'Verliehene Preise'] },
    { t: 'Rechtliches', l: ['Impressum', 'Datenschutzerklärung', 'Haftungsausschluss', 'Erklärung zur Barrierefreiheit'] },
  ];
  return (
    <footer style={{ background: 'var(--bgm-teal-900)', color: '#fff', marginTop: 64 }}>
      <div style={{ maxWidth: 1152, margin: '0 auto', padding: '64px 32px 32px' }}>
        <div style={{ display: 'grid', gridTemplateColumns: '1.4fr repeat(4, 1fr)', gap: 40, marginBottom: 48 }}>
          <div>
            <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 16 }}>
              <img src="../../assets/logo-mark.png" alt="" style={{ height: 56, width: 'auto', filter: 'brightness(0) invert(1)' }} />
              <div style={{ fontWeight: 700, fontSize: 14, lineHeight: 1.3 }}>Begleitung geflüchteter<br/>Menschen e.V., Pulheim</div>
            </div>
            <p style={{ fontSize: 13, lineHeight: 1.6, color: 'var(--bgm-teal-300)', marginBottom: 16 }}>
              Praxisnah, hilfsbereit, herzlich. Seit 2016.
            </p>
            <div style={{ fontSize: 13, color: 'var(--bgm-teal-300)' }}>
              <Icon name="map-pin" color="gold" size={14} /> Gemeindehaus, 50259 Pulheim<br/>
              <Icon name="envelope-simple" color="gold" size={14} /> info@bgm-pulheim.de
            </div>
          </div>
          {cols.map(c => (
            <div key={c.t}>
              <div style={{ fontSize: 12, fontWeight: 700, letterSpacing: '.08em', textTransform: 'uppercase', color: 'var(--bgm-gold-300)', marginBottom: 14 }}>{c.t}</div>
              <ul style={{ listStyle: 'none', padding: 0, margin: 0, display: 'flex', flexDirection: 'column', gap: 8 }}>
                {c.l.map(li => <li key={li}><a href="#" style={{ color: '#fff', textDecoration: 'none', fontSize: 14 }}>{li}</a></li>)}
              </ul>
            </div>
          ))}
        </div>
        <div style={{ borderTop: '1px solid rgba(255,255,255,.15)', paddingTop: 20, display: 'flex', justifyContent: 'space-between', fontSize: 12, color: 'var(--bgm-teal-300)' }}>
          <div>© 2026 Begleitung geflüchteter Menschen e.V., Pulheim</div>
          <div>Gefördert durch die Stadt Pulheim</div>
        </div>
      </div>
    </footer>
  );
}
window.Footer = Footer;
