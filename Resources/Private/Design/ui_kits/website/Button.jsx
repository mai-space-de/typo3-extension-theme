function Button({ variant = 'primary', children, onClick, icon, type = 'button', as = 'button', href }) {
  const base = {
    display: 'inline-flex', alignItems: 'center', gap: 8,
    padding: '11px 20px', borderRadius: 4,
    fontFamily: 'inherit', fontWeight: 600, fontSize: 15,
    border: 0, cursor: 'pointer', textDecoration: 'none',
    transition: 'background 120ms cubic-bezier(.2,0,.2,1), color 120ms cubic-bezier(.2,0,.2,1), border-color 120ms',
    lineHeight: 1.2,
  };
  const variants = {
    primary:   { background: 'var(--bgm-teal-700)', color: '#fff' },
    volunteer: { background: 'var(--bgm-gold-300)', color: 'var(--bgm-teal-900)' },
    outline:   { background: 'transparent', color: 'var(--bgm-teal-800)', border: '2px solid var(--bgm-teal-700)', padding: '9px 18px' },
    ghost:     { background: 'transparent', color: 'var(--bgm-teal-800)', padding: '9px 6px' },
    onTeal:    { background: '#fff', color: 'var(--bgm-teal-800)' },
  };
  const [hover, setHover] = React.useState(false);
  const hoverStyles = {
    primary:   { background: 'var(--bgm-teal-800)' },
    volunteer: { background: 'var(--bgm-gold-500)' },
    outline:   { background: 'var(--bgm-teal-050)' },
    ghost:     { color: 'var(--bgm-teal-900)', textDecoration: 'underline' },
    onTeal:    { background: 'var(--bgm-teal-050)' },
  };
  const style = { ...base, ...variants[variant], ...(hover ? hoverStyles[variant] : {}) };
  const Tag = as === 'a' ? 'a' : 'button';
  const props = as === 'a' ? { href } : { onClick, type };
  return (
    <Tag {...props} style={style} onMouseEnter={() => setHover(true)} onMouseLeave={() => setHover(false)}>
      {icon ? <Icon name={icon} color={variant === 'volunteer' ? 'teal' : (variant === 'primary' ? 'white' : 'teal')} size={16} /> : null}
      {children}
    </Tag>
  );
}
window.Button = Button;
