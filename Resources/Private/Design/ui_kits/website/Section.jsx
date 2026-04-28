function Section({ eyebrow, title, lead, children, background = '#fff', dense }) {
  return (
    <section style={{ background, padding: dense ? '48px 0' : '80px 0' }}>
      <div style={{ maxWidth: 1152, margin: '0 auto', padding: '0 32px' }}>
        {(eyebrow || title || lead) && (
          <header style={{ marginBottom: 40, maxWidth: 720 }}>
            {eyebrow ? <div className="eyebrow" style={{ marginBottom: 10 }}>{eyebrow}</div> : null}
            {title ? <h2 style={{ margin: '0 0 14px' }}>{title}</h2> : null}
            {lead ? <p className="lead" style={{ margin: 0 }}>{lead}</p> : null}
          </header>
        )}
        {children}
      </div>
    </section>
  );
}
window.Section = Section;
