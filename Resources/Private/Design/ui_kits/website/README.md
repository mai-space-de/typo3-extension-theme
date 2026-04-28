# BGM Pulheim — Website UI kit

A high-fidelity recreation of the public website of Begleitung geflüchteter
Menschen e.V. Pulheim. Components are React (inline JSX, no build) and
share tokens via `../../colors_and_type.css`.

## Files
- `index.html` — interactive click-through (home → angebote → detail → mitwirken form)
- `Header.jsx` — top nav with language switcher
- `Hero.jsx` — full-bleed welcome with logo + lead copy
- `OfferCard.jsx` — the signature double-frame offer card
- `OfferGrid.jsx` — 4-up grid of offer cards
- `Section.jsx` — page section wrapper with eyebrow + heading
- `NewsCard.jsx` — news / press release card
- `EventRow.jsx` — calendar row for upcoming events
- `MitwirkenForm.jsx` — volunteer signup form
- `Footer.jsx` — site footer with sitemap + legal links
- `Button.jsx` — buttons (primary / volunteer / outline / ghost)
- `Icon.jsx` — Phosphor icon wrapper

## Coverage notes
The home view, offers index, single offer detail, and Mitwirken signup are
covered. Login, story-wall, mentoring, FAQ/Glossar, and event-anmeldung
flows are referenced in the prompt but **not yet implemented** — flagged
for the next iteration.

The visual reference is a single screenshot of four offer cards. The header,
hero, news section, event list, and footer are designed by extending the
brand foundations and **need approval**.
