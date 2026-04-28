function OfferCard({ icon, title, body, cta, accent = 'teal', onClick }) {
  const isGold = accent === 'gold';
  const borderColor = isGold ? 'var(--bgm-gold-500)' : 'var(--bgm-teal-700)';
  const iconColor = isGold ? 'teal' : 'gold';
  const [hover, setHover] = React.useState(false);
  return (
    <div
      onMouseEnter={() => setHover(true)}
      onMouseLeave={() => setHover(false)}
      style={{
        position: 'relative', background: '#fff',
        border: `2px solid ${borderColor}`,
        padding: '40px 26px 26px',
        marginTop: 28, marginLeft: 28,
        transition: 'transform 200ms cubic-bezier(.2,0,.2,1)',
        transform: hover ? 'translate(-2px,-2px)' : 'none',
      }}
    >
      <div style={{
        position: 'absolute', top: -28, left: -28,
        width: 64, height: 64, background: '#fff',
        border: `2px solid ${borderColor}`,
        display: 'flex', alignItems: 'center', justifyContent: 'center',
      }}>
        <Icon name={icon} color={iconColor} size={36} />
      </div>
      <h3 style={{ margin: '6px 0 10px', fontSize: 22, color: 'var(--bgm-teal-900)' }}>{title}</h3>
      <p style={{ fontSize: 15, lineHeight: 1.65, color: 'var(--bgm-ink-2)', marginBottom: 18, minHeight: 100 }}>{body}</p>
      <Button variant={isGold ? 'volunteer' : 'primary'} onClick={onClick}>{cta}</Button>
    </div>
  );
}
window.OfferCard = OfferCard;
