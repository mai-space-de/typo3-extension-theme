function Icon({ name, color = 'teal', size = 20, style = {} }) {
  const c = color === 'gold' ? 'var(--bgm-gold-500)'
          : color === 'white' ? '#fff'
          : color === 'ink' ? 'var(--bgm-ink)'
          : 'var(--bgm-teal-700)';
  return <i className={`ph-fill ph-${name}`} style={{ fontSize: size, color: c, lineHeight: 1, ...style }} />;
}
window.Icon = Icon;
