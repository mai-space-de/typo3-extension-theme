---
name: bgm-pulheim-design
description: Use this skill to generate well-branded interfaces and assets for Begleitung geflüchteter Menschen e.V. Pulheim (BGM Pulheim), either for production or throwaway prototypes/mocks/etc. Contains essential design guidelines, colors, type, fonts, assets, and UI kit components for prototyping.
user-invocable: true
---

Read the README.md file within this skill, and explore the other available files.

The skill bundles:
- `README.md` — context, voice/tone, visual foundations, iconography
- `colors_and_type.css` — drop-in CSS variables + semantic element styles
- `assets/` — logo lockup, mark, favicon
- `preview/` — small HTML specimens for each token / component
- `ui_kits/website/` — React component recreation of the public website

If creating visual artifacts (slides, mocks, throwaway prototypes, etc),
copy assets out and create static HTML files for the user to view. Always
include `colors_and_type.css` and `https://unpkg.com/@phosphor-icons/web`
for the icon system.

If working on production code, copy assets and read the rules in README.md
to become an expert in designing with this brand. The signature visual
device is the **double-frame offer card with overlapping icon badge** in
either teal (`#1f6e6e`) or gold (`#d8b222`) — use it for any
"call-to-action with explanation" surface.

Voice rules in short:
- German, warm, plain, dignified — never patronising.
- "wir" for the org; "Sie" for volunteers/donors; "euch/ihr" for community.
- Refer to refugees as **geflüchtete Menschen** or **Neubürger** —
  never "Flüchtlinge".
- No emoji. Triplet adjectives are a recurring stylistic device
  ("praxisnah, hilfsbereit, herzlich").

If the user invokes this skill without any other guidance, ask them what
they want to build or design, ask some questions, and act as an expert
designer who outputs HTML artifacts _or_ production code, depending on
the need.
