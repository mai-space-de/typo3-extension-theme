function Hero({ onCta }) {
  return (
    <section style={{
      background: 'linear-gradient(180deg, var(--bgm-teal-050) 0%, #fff 100%)',
      borderBottom: '1px solid var(--bgm-line-2)',
    }}>
      <div style={{
        maxWidth: 1152, margin: '0 auto', padding: '64px 32px 72px',
        display: 'grid', gridTemplateColumns: '1.4fr 1fr', gap: 48, alignItems: 'center',
      }}>
        <div>
          <div className="eyebrow" style={{ marginBottom: 12 }}>Willkommen in Pulheim</div>
          <h1 style={{ fontSize: 56, lineHeight: 1.1, margin: '0 0 20px', color: 'var(--bgm-teal-900)' }}>
            Gemeinsam ankommen.<br/>Gemeinsam mitgestalten.
          </h1>
          <p className="lead" style={{ maxWidth: 540, marginBottom: 28 }}>
            Wir begleiten geflüchtete Menschen in Pulheim — bei Fragen des Alltags,
            beim Deutschlernen und auf dem Weg in unsere Gesellschaft.
            Praxisnah, hilfsbereit, herzlich.
          </p>
          <div style={{ display: 'flex', gap: 12 }}>
            <Button variant="primary" onClick={() => onCta('angebote')}>Unsere Angebote</Button>
            <Button variant="volunteer" icon="hand-heart" onClick={() => onCta('mitwirken')}>Jetzt mithelfen</Button>
          </div>
        </div>
        <div style={{ position: 'relative', display: 'flex', justifyContent: 'center' }}>
          <div style={{
            background: '#fff', border: '2px solid var(--bgm-teal-700)',
            padding: 32, display: 'flex', alignItems: 'center', justifyContent: 'center',
            position: 'relative',
          }}>
            <img src="../../assets/logo-full.png" alt="BGM Pulheim" style={{ height: 280, width: 'auto', display: 'block' }} />
            <div style={{
              position: 'absolute', top: -18, right: -18,
              background: 'var(--bgm-gold-300)', color: 'var(--bgm-teal-900)',
              padding: '8px 14px', fontSize: 13, fontWeight: 700, letterSpacing: '.04em',
              border: '2px solid var(--bgm-gold-500)', textTransform: 'uppercase',
            }}>seit 2016</div>
          </div>
        </div>
      </div>
    </section>
  );
}
window.Hero = Hero;
