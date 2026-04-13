# Custom Elements — Reality Check

> **Purpose:** Evaluate every registered CType and determine whether it belongs in a
> general-purpose website theme, should live in a dedicated extension, or represents
> a controller / backend action rather than a content element.
>
> **Last updated:** 2026-04-12 — reflects the final implemented state after the
> CType audit. Removed elements are listed in the appendix for reference.

## Legend

| Icon | Status | Meaning |
|------|--------|---------|
| ✅ | **Theme** | General enough — belongs in a website theme extension |
| ⚠️ | **Extension** | Too specific — should be a feature of a dedicated extension |
| ❌ | **Controller** | Not a content element — belongs in a controller / middleware / action |

---

## Basic Elements

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 1 | `maispace_text` | basic | Plain-text content block | ✅ Theme | Fundamental building block of every website |
| 2 | `maispace_heading` | basic | Heading with optional subheader | ✅ Theme | Universal element present in every design system |
| 3 | `maispace_richtext` | basic | Rich-text (CKEditor) content | ✅ Theme | Core content editing, expected in any theme |
| 4 | `maispace_image` | basic | Single image with caption | ✅ Theme | Every website needs image rendering |
| 5 | `maispace_video` | basic | Embedded / uploaded video | ✅ Theme | Media is a standard theme concern |
| 6 | `maispace_audio` | basic | Embedded / uploaded audio | ✅ Theme | Media is a standard theme concern |
| 7 | `maispace_button` | basic | Call-to-action button / link | ✅ Theme | Common UI primitive in every design system |
| 8 | `maispace_linklist` | basic | List of links | ✅ Theme | Standard navigation / utility element |
| 9 | `maispace_icon` | basic | Standalone icon element | ✅ Theme | Design-system primitive for decorative / informational icons |
| 10 | `maispace_divider` | basic | Visual separator / horizontal rule | ✅ Theme | Layout utility found in every theme |
| 11 | `maispace_breadcrumb` | basic | Breadcrumb navigation trail | ✅ Theme | Standard navigational aid expected in most themes |
| 12 | `maispace_socialmedia` | basic | Social media icon links | ✅ Theme | Very common footer / header element for linking to social profiles |
| 13 | `maispace_embed` | basic | Generic oEmbed / iframe wrapper (YouTube, Vimeo, etc.) | ✅ Theme | Covers third-party embeds with privacy-consent wrapper |
| 14 | `maispace_spacer` | basic | Vertical whitespace / spacing block | ✅ Theme | Simple layout helper complementing `maispace_divider` |
| 15 | `maispace_callout` | basic | Editorial callout / notice box (note, tip, important) | ✅ Theme | Common content-aside pattern for highlighting key information |
| 16 | `maispace_iconlist` | basic | List with a custom icon per item | ✅ Theme | Very common pattern for feature lists, checklists, and benefit summaries |
| 17 | `maispace_textcolumns` | basic | Multi-column text flow (CSS columns) | ✅ Theme | Purely presentational layout for long-form text |

## Page Sections

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 18 | `maispace_hero` | page | Full-width hero banner with text + image + CTA | ✅ Theme | Very common top-of-page pattern |
| 19 | `maispace_banner` | page | Promotional banner with image + text overlay | ✅ Theme | Generic enough for any website |
| 20 | `maispace_cta` | page | Call-to-action section (heading + body + link) | ✅ Theme | Standard marketing section in most themes |
| 21 | `maispace_mediatext` | page | Side-by-side media + text block | ✅ Theme | Classic content layout pattern |

## Cards & Components

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 22 | `maispace_card` | components | Generic card (image + heading + body + link) | ✅ Theme | Universal UI pattern |
| 23 | `maispace_teaser` | components | Teaser / preview card linking to another page | ✅ Theme | Standard component for content previews |
| 24 | `maispace_featurebox` | components | Icon/image + heading + text feature highlight | ✅ Theme | Common section on landing pages |
| 25 | `maispace_profile` | components | Person profile card (photo + name + bio) | ✅ Theme | Team / about-us pages are part of most websites |
| 26 | `maispace_testimonial` | components | Customer quote with attribution + avatar | ✅ Theme | Widely used social-proof pattern |
| 27 | `maispace_quote` | components | Styled block-quote with attribution | ✅ Theme | Common editorial element |
| 28 | `maispace_logo` | components | Single logo image with optional link | ✅ Theme | Used in partner / sponsor sections |
| 29 | `maispace_logoshowcase` | components | Logo carousel / grid | ✅ Theme | Common partner / client showcase pattern |
| 30 | `maispace_statistic` | components | Key-figure counter (number + label) | ✅ Theme | Frequently used in about / landing pages |
| 31 | `maispace_badge` | components | Small label / tag element | ✅ Theme | Generic UI micro-component |
| 32 | `maispace_contactinfo` | components | Structured contact block (address, phone, email, hours) | ✅ Theme | Nearly every business website needs a static contact details component |

## UI Widgets

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 33 | `maispace_slider` | widgets | Image / content carousel | ✅ Theme | Common interactive component across all website types |
| 34 | `maispace_accordion` | widgets | Collapsible content panels | ✅ Theme | Standard UI widget for FAQs, details, etc. |
| 35 | `maispace_tabs` | widgets | Tabbed content panels | ✅ Theme | Standard UI widget for grouped content |
| 36 | `maispace_modal` | widgets | Overlay / lightbox dialog | ✅ Theme | Generic UI widget, content is static HTML |
| 37 | `maispace_timeline` | widgets | Chronological event list | ✅ Theme | Useful for about-us / history pages — generic enough |
| 38 | `maispace_faq` | widgets | FAQ list (question + answer pairs) | ✅ Theme | Very common on informational websites; structurally similar to accordion |
| 39 | `maispace_beforeafter` | widgets | Before / after image comparison slider | ✅ Theme | Purely presentational comparison widget — common in portfolios, case studies |
| 40 | `maispace_steps` | widgets | Step indicator / process visualisation | ✅ Theme | Useful for how-to guides, onboarding flows, and process explanations |

## Forms & Engagement

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 41 | `maispace_form` | forms | Generic TYPO3 form framework integration | ✅ Theme | Thin wrapper around EXT:form — theming concern |
| 42 | `maispace_newsletter` | forms | Newsletter signup form | ✅ Theme | Thin wrapper delegating to `mai_newsletter` — purely a presentation concern |
| 43 | `maispace_map` | forms | Embedded map (address / coordinates) | ✅ Theme | Presentational component with lat/lng/zoom fields; API key managed in site config |

## Data & Media

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 44 | `maispace_table` | data | Data table (textTable rendering) | ✅ Theme | Standard content type for tabular data |
| 45 | `maispace_datalist` | data | Definition list / key-value pairs | ✅ Theme | Basic HTML structure, theme-level concern |
| 46 | `maispace_gallery` | data | Image gallery / lightbox grid | ✅ Theme | Common media presentation pattern |
| 47 | `maispace_filelist` | data | Download list of file assets | ✅ Theme | Standard content type for document downloads |
| 48 | `maispace_chart` | data | Data visualisation chart | ✅ Theme | Presentational chart with bodytext data input; JS library managed in theme |
| 49 | `maispace_codeblock` | data | Syntax-highlighted code snippet | ✅ Theme | Useful for documentation / tech sites; purely presentational |
| 50 | `maispace_progressbar` | data | Progress / percentage bar | ✅ Theme | Static percentage indicator (e.g. skill bars, fundraiser progress) — purely presentational |
| 51 | `maispace_rating` | data | Star / score rating display | ✅ Theme | Static read-only rating display (no user input); dynamic ratings belong in a review extension |

## Feedback & State

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 52 | `maispace_alert` | feedback | Inline alert / info box (success, warning, danger, info) | ✅ Theme | Standard UI pattern for editorial notices |

## Sections & Layouts

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 53 | `maispace_section_full` | sections | Full-width single-column container | ✅ Theme | Core layout building block (b13/container) |
| 54 | `maispace_section_50_50` | sections | Two equal columns (50 / 50) | ✅ Theme | Core layout building block |
| 55 | `maispace_section_66_33` | sections | Two columns (⅔ + ⅓) | ✅ Theme | Core layout building block |
| 56 | `maispace_section_33_66` | sections | Two columns (⅓ + ⅔) | ✅ Theme | Core layout building block |
| 57 | `maispace_section_3col` | sections | Three equal columns | ✅ Theme | Core layout building block |
| 58 | `maispace_section_4col` | sections | Four equal columns | ✅ Theme | Core layout building block |
| 59 | `maispace_section_sidebar_r` | sections | Main content + right sidebar | ✅ Theme | Core layout building block |
| 60 | `maispace_section_sidebar_l` | sections | Left sidebar + main content | ✅ Theme | Core layout building block |

---

## Summary

| Status | Count | Elements |
|--------|-------|----------|
| ✅ Theme | 60 | text, heading, richtext, image, video, audio, button, linklist, icon, divider, breadcrumb, socialmedia, embed, spacer, callout, iconlist, textcolumns, hero, banner, cta, mediatext, card, teaser, featurebox, profile, testimonial, quote, logo, logoshowcase, statistic, badge, contactinfo, slider, accordion, tabs, modal, timeline, faq, beforeafter, steps, form, newsletter, map, table, datalist, gallery, filelist, chart, codeblock, progressbar, rating, alert, section_full, section_50_50, section_66_33, section_33_66, section_3col, section_4col, section_sidebar_r, section_sidebar_l |

---

## Appendix — Removed CTypes

The following CTypes were removed during the 2026-04-12 audit and **must not** be re-added to `mai_theme`.

| CType | Former group | Reason | Where it belongs |
|-------|-------------|--------|-----------------|
| `maispace_pricebox` | components | ⚠️ Extension | Pricing is a domain concern — shop / SaaS extension (e.g. `mai_shop`) |
| `maispace_event` | components | ⚠️ Extension | Events need dedicated records and calendar views — event extension (e.g. `mai_events`) |
| `maispace_jobposting` | components | ⚠️ Extension | Job boards require structured data and application workflows — recruitment extension (e.g. `mai_jobs`) |
| `maispace_search` | forms | ⚠️ Extension | Search requires an indexer and result rendering — search extension (e.g. EXT:ke_search) |
| `maispace_notification` | feedback | ❌ Controller | Transient toast messages are triggered by a controller / flash-message mechanism, not placed as static content |
| `maispace_spinner` | feedback | ❌ Controller | Loading spinners indicate async JS state — not a static content element |
| `maispace_emptystate` | feedback | ❌ Controller | Empty states are conditional UI rendered when a data source returns nothing — belongs in a Fluid partial / controller |
| `maispace_confirmation` | feedback | ❌ Controller | Confirmation dialogs respond to user actions — require controller logic to trigger and resolve |
