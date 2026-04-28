function MitwirkenForm({ onSubmit }) {
  const [data, setData] = React.useState({
    vorname: '', nachname: '', email: '', telefon: '',
    bereich: 'Pate / Patin', sprachen: '', verfuegbar: 'Ich habe heute Zeit',
    nachricht: '', dsg: false,
  });
  const [done, setDone] = React.useState(false);

  const set = (k, v) => setData(d => ({ ...d, [k]: v }));
  const inputStyle = {
    width: '100%', fontFamily: 'inherit', fontSize: 15,
    padding: '11px 13px', border: '1px solid var(--bgm-line)',
    borderRadius: 4, background: '#fff', color: 'var(--bgm-ink)',
    boxSizing: 'border-box', outline: 'none',
  };
  const labelStyle = { display: 'block', fontSize: 13, fontWeight: 600, color: 'var(--bgm-teal-900)', marginBottom: 6 };

  if (done) {
    return (
      <div style={{ background: '#fff', border: '2px solid var(--bgm-gold-500)', padding: 32, maxWidth: 640 }}>
        <div style={{ display: 'flex', gap: 16, alignItems: 'flex-start' }}>
          <Icon name="hand-heart" color="gold" size={40} />
          <div>
            <h3 style={{ margin: '0 0 8px' }}>Vielen Dank, {data.vorname || 'lieber Mensch'}!</h3>
            <p style={{ margin: 0, color: 'var(--bgm-ink-2)' }}>Wir melden uns innerhalb der nächsten Woche per E-Mail bei Ihnen. Ihre Hilfe macht den Unterschied.</p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <form
      onSubmit={(e) => { e.preventDefault(); setDone(true); onSubmit && onSubmit(data); }}
      style={{ background: '#fff', border: '2px solid var(--bgm-teal-700)', padding: 32, maxWidth: 640 }}
    >
      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
        <div><label style={labelStyle}>Vorname</label><input style={inputStyle} value={data.vorname} onChange={e => set('vorname', e.target.value)} required /></div>
        <div><label style={labelStyle}>Nachname</label><input style={inputStyle} value={data.nachname} onChange={e => set('nachname', e.target.value)} required /></div>
      </div>
      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
        <div><label style={labelStyle}>E-Mail</label><input type="email" style={inputStyle} value={data.email} onChange={e => set('email', e.target.value)} required /></div>
        <div><label style={labelStyle}>Telefon (optional)</label><input style={inputStyle} value={data.telefon} onChange={e => set('telefon', e.target.value)} /></div>
      </div>
      <div style={{ marginBottom: 16 }}>
        <label style={labelStyle}>Wo möchten Sie helfen?</label>
        <select style={inputStyle} value={data.bereich} onChange={e => set('bereich', e.target.value)}>
          <option>Pate / Patin</option>
          <option>Bunter Tisch — Küche</option>
          <option>Sprachvermittlung</option>
          <option>Begleitung zu Behörden</option>
          <option>Ladies Club</option>
          <option>Männer International</option>
          <option>Mentoring</option>
        </select>
      </div>
      <div style={{ marginBottom: 16 }}>
        <label style={labelStyle}>Sprachen, die Sie sprechen</label>
        <input style={inputStyle} placeholder="z.B. Deutsch, Arabisch, Englisch" value={data.sprachen} onChange={e => set('sprachen', e.target.value)} />
      </div>
      <div style={{ marginBottom: 16 }}>
        <label style={labelStyle}>Wann sind Sie verfügbar?</label>
        <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
          {['Ich habe heute Zeit', 'Diese Woche', 'Wochenende', 'Regelmäßig'].map(opt => (
            <label key={opt} style={{
              padding: '8px 14px', fontSize: 13, fontWeight: 600,
              border: `1px solid ${data.verfuegbar === opt ? 'var(--bgm-teal-700)' : 'var(--bgm-line)'}`,
              background: data.verfuegbar === opt ? 'var(--bgm-teal-050)' : '#fff',
              color: data.verfuegbar === opt ? 'var(--bgm-teal-900)' : 'var(--bgm-ink-2)',
              borderRadius: 4, cursor: 'pointer',
            }}>
              <input type="radio" name="verf" value={opt} checked={data.verfuegbar === opt}
                onChange={e => set('verfuegbar', e.target.value)} style={{ display: 'none' }} />
              {opt}
            </label>
          ))}
        </div>
      </div>
      <div style={{ marginBottom: 18 }}>
        <label style={labelStyle}>Nachricht (optional)</label>
        <textarea rows="3" style={{ ...inputStyle, resize: 'vertical' }} value={data.nachricht} onChange={e => set('nachricht', e.target.value)} />
      </div>
      <label style={{ display: 'flex', alignItems: 'flex-start', gap: 10, fontSize: 13, color: 'var(--bgm-ink-2)', marginBottom: 22 }}>
        <input type="checkbox" checked={data.dsg} onChange={e => set('dsg', e.target.checked)} required />
        <span>Ich habe die <a href="#" style={{ color: 'var(--bgm-teal-700)' }}>Datenschutzerklärung</a> gelesen und stimme der Verarbeitung meiner Daten zu.</span>
      </label>
      <Button variant="volunteer" icon="hand-heart" type="submit">Ich möchte mithelfen</Button>
    </form>
  );
}
window.MitwirkenForm = MitwirkenForm;
