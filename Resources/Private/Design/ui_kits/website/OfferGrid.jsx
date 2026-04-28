function OfferGrid({ onOpen, onMitwirken }) {
  const offers = [
    { id: 'bunter-tisch', icon: 'map-pin',   title: 'Bunter Tisch',         body: 'Wir kochen für euch mit Vorsuppe oder Salat ein Hauptgericht. Manchmal gibt es noch ein Stück Kuchen als Nachtisch!', cta: 'Mehr erfahren', accent: 'teal' },
    { id: 'mitwirken',    icon: 'hand-heart',title: 'Mitwirken',            body: 'Begleiten Sie Familien oder Einzelpersonen bei den Fragen des Alltags in der neuen Umgebung von Pulheim als Pate oder Patin.', cta: 'Jetzt mithelfen', accent: 'gold' },
    { id: 'maenner',      icon: 'user',      title: 'Männer International', body: 'Männer International ist eine offene Runde zum Deutschlernen, Austauschen und gemeinsamen Entdecken des Lebens in Deutschland – praxisnah, hilfsbereit, herzlich.', cta: 'Zu Männer International', accent: 'teal' },
    { id: 'ladies',       icon: 'user-circle', title: 'Ladies Club',        body: 'In vertrauensvoller Atmosphäre bieten Doris und Bettina Frauen aus aller Welt Austausch, Ermutigung und ein buntes Programm für Herz und Seele.', cta: 'Zum Ladies Club', accent: 'teal' },
  ];
  return (
    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', columnGap: 36, rowGap: 36 }}>
      {offers.map(o => (
        <OfferCard key={o.id} {...o}
          onClick={() => o.id === 'mitwirken' ? onMitwirken() : onOpen(o.id)} />
      ))}
    </div>
  );
}
window.OfferGrid = OfferGrid;
