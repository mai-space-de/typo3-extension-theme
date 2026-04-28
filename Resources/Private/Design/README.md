# BGM Pulheim — Design System

**Begleitung geflüchteter Menschen e.V., Pulheim** is a volunteer-run
non-profit that supports refugees who have come to Pulheim (a town near
Cologne, Germany) seeking protection — politically, racially, gender-related,
through war or religious persecution. The organisation accompanies new
arrivals as **Lotsen** ("pilots") through Germany's social system, public
authorities, and everyday cultural conventions, and runs regular community
groups (Bunter Tisch, Ladies Club, Männer International), language support,
and integration events.

The design system in this repository captures the brand's visual + content
voice and provides reusable tokens, components, and a UI kit so future
designers (human or agent) can build new pages, materials, and prototypes
that feel unmistakably "BGM Pulheim".

---

## Sources reviewed

| Source | Notes |
|---|---|
| `uploads/BGM Login Logo.png` | Full vertical logo lockup (flower + pavilion mark + wordmark) |
| `uploads/favicon.png` | Square logo mark — flower in pavilion with side rays |
| `uploads/Bildschirmfoto 2026-04-28 um 11.03.51.png` | Reference screenshot of 4 offer-cards (Bunter Tisch, Mitwirken, Männer International, Ladies Club) — shows the signature "double-frame card" pattern, button styles, and icon treatment |
| Brief / IA from project prompt | Full sitemap, planned features (Mitmach-Matrix, Story Wall, Multi-lang via DeepL, FAQ, Mentoring, etc.) |

> No production codebase or Figma file was provided. The visual foundations
> below are reverse-engineered from the screenshot and logo, and font
> families are **substituted from Google Fonts**. Please replace the
> substituted fonts with the licensed family in use, if different.

---

## Index

| File / folder | What it is |
|---|---|
| `README.md` | This document — context, content & visual foundations, iconography |
| `SKILL.md` | Agent Skill manifest — drop into Claude Code as `bgm-pulheim-design` |
| `colors_and_type.css` | Source of truth for color, type, spacing, radius, shadow tokens + semantic element styles |
| `assets/` | Logos (full lockup + mark), favicon, reference screenshot |
| `preview/` | Card-sized HTML specimens that populate the Design System tab |
| `ui_kits/website/` | High-fidelity recreation of the public website — components + click-through `index.html` |
| `fonts/` | (none yet — we use Google Fonts via @import; add licensed files here) |

---

## Content fundamentals

**Language.** Primary content is **German**. Multi-language support via
DeepL is on the roadmap (Arabic, Ukrainian, English, Farsi, Turkish are the
likely targets given the audience). Always design with text-expansion
headroom — Arabic right-to-left and German compound words both stress
layouts.

**Voice.** Warm, plain, dignified, never patronising. The reader is an
adult who is new to a system, not a child. Sentences are short and
declarative. Verbs first when giving direction ("Begleiten Sie Familien…",
"Wir kochen für euch…"). Imagery in language is concrete: "ein Stück
Kuchen", "Pate oder Patin", "Herz und Seele".

**Pronouns.**
- The org speaks as **wir** ("Wir unterstützen…", "Wir kochen für euch…").
- Volunteers and donors are addressed formally as **Sie** ("Begleiten Sie
  Familien…").
- Programme members are addressed warmly as **euch / ihr** in social
  contexts ("Wir kochen für euch").
- Refugees and new arrivals are referred to as **geflüchtete Menschen** or
  **Neubürger**, never "Flüchtlinge".

**Casing & punctuation.**
- German sentence case for headings (only the first word and nouns
  capitalised, per orthography).
- No ALL-CAPS shouting in body copy. Eyebrow labels may use uppercase
  with letter-spacing.
- Em-dash with spaces ( – ) German-style; never the tight em-dash used in
  US English.
- Numbers in body copy: spell out "drei Gruppen" up to twelve, digits
  beyond.

**Tone examples (lifted from the reference screenshot).**
- *Bunter Tisch:* "Wir kochen für euch mit Vorsuppe oder Salat ein
  Hauptgericht. Manchmal gibt es noch ein Stück Kuchen als Nachtisch!"
- *Ladies Club:* "In vertrauensvoller Atmosphäre bieten Doris und Bettina
  Frauen aus aller Welt Austausch, Ermutigung und ein buntes Programm für
  Herz und Seele."
- *Männer International:* "…eine offene Runde zum Deutschlernen,
  Austauschen und gemeinsamen Entdecken des Lebens in Deutschland –
  praxisnah, hilfsbereit, herzlich."
- *Mitwirken CTA:* "Begleiten Sie Familien oder Einzelpersonen bei den
  Fragen des Alltags…"

Note the rhythm: a short triplet of adjectives ("praxisnah, hilfsbereit,
herzlich") is a recurring stylistic device. Use it.

**No emoji.** The brand is volunteer/civic and reads as serious-but-warm.
Emoji would feel out of place. Use the icon system instead (see below).

**CTAs.** Verb + object, never "Click here". Examples in the wild: *Mehr
erfahren · Jetzt mithelfen · Zum Ladies Club · Zu Männer International ·
Spenden · Newsletter abonnieren*.

**Forbidden phrases.** "Integration" used as a buzzword without substance;
"Flüchtlinge" (use *geflüchtete Menschen*); generic AI-tone words like
"empower", "leverage", "innovative".

---

## Visual foundations

### Color

Two-color brand: **petrol teal** and **warm gold**, on **off-white**.
Both brand colors carry meaning — teal is the organisation, gold is action
(volunteer, donate, get involved).

| Role | Token | Hex |
|---|---|---|
| Primary teal (logo, headings, primary buttons) | `--bgm-teal-700` | `#1f6e6e` |
| Deep teal (heading on light, hover) | `--bgm-teal-900` | `#0e3f3f` |
| Decorative teal | `--bgm-teal-300` | `#9bbbbb` |
| Primary gold (icons, accents) | `--bgm-gold-500` | `#d8b222` |
| Light gold (button fill, soft surface) | `--bgm-gold-300` | `#ead687` |
| Surface | `--bgm-surface` | `#f5f5f5` |
| Card | `--bgm-card` | `#ffffff` |
| Ink (body text) | `--bgm-ink` | `#1a1a1a` |

Gradients and bluish-purple tints are **not part of the brand**. Solid
fills only.

### Type

- **Display & body:** `Source Sans 3` (substituted via Google Fonts).
  Weights used: 400 / 600 / 700 / 900.
- **Optional accent serif:** `Source Serif 4` for pull-quotes and
  testimonials only. Avoid for UI.
- Headings are **bold**, generously sized, and tinted to deep teal rather
  than pure black — softer authority.
- Body lines hover around `1.55–1.7` for German legibility.
- Minimum body size in production: 16px. For older readers (likely audience
  for the Mitwirken funnel), 18px is preferred for body.

### Spacing & layout

- 4px base unit; the scale is `4 · 8 · 12 · 16 · 24 · 32 · 48 · 64 · 96`.
- Reading width caps around 660px (`--max-prose`); page width caps at
  1152px (`--max-content`).
- Generous vertical breathing: section padding 64–96px desktop, 48px mobile.
- The signature offer-card layout is a **single column on mobile, 2-up on
  tablet, 2 or 4-up on desktop** — never crowded.

### Cards & borders — the signature pattern

The defining visual device is the **double-frame offer card**: a
rectangular content card with a 2px teal (or gold) outline and a small
square "icon badge" (also outlined, slightly offset, slightly overlapping
the card's top-left or top-right corner). The badge border colour matches
the card's border colour.

Rules:
- Card border: `2px solid` in either `--bgm-teal-700` (default) or
  `--bgm-gold-500` (when promoting volunteer/donate actions).
- Card corners: square (radius 0–2px). The brand reads as civic /
  institutional, not consumer-app rounded.
- Icon badge: 64×64px, white fill, same 2px border, sits half-overlapping
  the card edge.
- Inside padding: 24–32px.
- No drop shadows on these cards. Elevation is communicated by colour and
  outline weight.

Other surfaces (forms, modals, news cards) may use `--shadow-md` for
subtle elevation, but always with low-opacity teal-tinted shadow.

### Buttons

Three flavours in the wild — all **square / radius 4px**, no shadows.

1. **Solid teal** — primary CTA (white text on `--bgm-teal-700`).
2. **Solid gold** — volunteer / donate CTA (`--bgm-teal-900` text on
   `--bgm-gold-300`).
3. **Outline / muted** — secondary action ("Zu Männer International").

Hover: darken solid fills by stepping one shade deeper. No scale animation.
Press: nudge background one further step deeper; no shrink.

### Iconography style

(See full ICONOGRAPHY section below.) Icons are filled, geometric, and
single-colour — either teal or gold to match the card outline.

### Imagery

Real photography of the community: warm tones, candid documentary feel,
people in mid-conversation rather than posed. No stock-photo handshakes.
B&W is acceptable for archival / press-release contexts. Avoid
heavily-graded or filtered looks.

When photography is unavailable, use the framed-icon card with no image —
the icon stands in.

### Animation

Restrained. Civic brand voice means no bouncing, no springs. Use
`var(--duration-fast)` (120ms) and `var(--duration-base)` (200ms) with
`cubic-bezier(.2,0,.2,1)` for hover, focus, modal fades. Page transitions:
none, or 200ms fade.

### Hover, focus, press

- **Hover:** background darkens one step on solid buttons; underline
  thickens on links; cards may darken their border.
- **Focus-visible:** 2px gold ring with 2px offset (`--color-focus-ring`).
  Always visible — accessibility-critical for older users.
- **Press:** colour shifts one step deeper, no scale change.
- **Disabled:** 40% opacity, no pointer events.

### Borders, radii, shadows summary

- Border thickness: `1px` (hairlines), `2px` (signature card outline),
  `3px` (rare, headers).
- Radii: 0 (institutional surfaces), 2px (small chips), 4px (default
  buttons, inputs), 999px (avatars / pills only).
- Shadows: `--shadow-sm/md/lg`, all teal-tinted, low opacity. Never the
  default browser-grey shadow.

### Use of transparency / blur

Sparingly. A 55% teal overlay on photography backgrounds for hero
sections is on-brand (`--bgm-overlay`). Backdrop-filter blur is fine on
sticky headers over photography. No frosted-glass UI panels.

---

## Iconography

The reference screenshot uses **filled, geometric, single-colour glyph
icons** in either teal or gold. Style matches **Phosphor Icons (fill
variant)** or **Material Symbols Rounded (filled)** very closely. We
substitute **Phosphor Icons** as the working set — please confirm.

Rules:
- One colour per icon: teal (`--bgm-teal-700`) or gold
  (`--bgm-gold-500`). Never multi-colour, never with internal gradients.
- Icon stroke style: filled. Outline icons are reserved for tertiary UI
  affordances (close, chevron).
- Sizes: 20px inline, 24px UI default, 40–48px in framed icon-badge.
- Always paired with a text label; never icon-only navigation in the
  public site (accessibility for new German speakers).
- Emoji and unicode dingbats are **not used** as icons.

Loading via CDN (working substitution):

```html
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<i class="ph-fill ph-map-pin" style="color: var(--bgm-gold-500); font-size: 40px"></i>
```

If the user provides original SVGs (e.g. the four offer-card glyphs), drop
them into `assets/icons/` and reference directly.

---

## Open questions / caveats

- Fonts substituted from Google Fonts — confirm or send licensed family.
- Icon set substituted with Phosphor Icons (fill) — confirm or send the
  original SVGs.
- No production codebase / Figma was attached — visual rules are
  reverse-engineered from the screenshot and logo. Expect to refine.
- The 4-card screenshot is the only UI artifact we have. Header, footer,
  forms, navigation, and detail pages are designed by extension and need
  approval.
