# Custom Elements — Reality Check

> **Purpose:** Evaluate every registered CType and determine whether it belongs in a
> general-purpose website theme, should live in a dedicated extension, or represents
> a controller / backend action rather than a content element.

## Legend

| Icon | Status | Meaning |
|------|--------|---------|
| ✅ | **Theme** | General enough — belongs in a website theme extension |
| ⚠️ | **Extension** | Too specific — should be a feature of a dedicated extension |
| ❌ | **Controller** | Not a content element — belongs in a controller / middleware / action |
| 🆕 | **Missing** | Suggested element not yet defined that a general theme could provide |

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

## Page Sections

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 11 | `maispace_hero` | page | Full-width hero banner with text + image + CTA | ✅ Theme | Very common top-of-page pattern |
| 12 | `maispace_banner` | page | Promotional banner with image + text overlay | ✅ Theme | Generic enough for any website |
| 13 | `maispace_cta` | page | Call-to-action section (heading + body + link) | ✅ Theme | Standard marketing section in most themes |
| 14 | `maispace_mediatext` | page | Side-by-side media + text block | ✅ Theme | Classic content layout pattern |

## Cards & Components

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 15 | `maispace_card` | components | Generic card (image + heading + body + link) | ✅ Theme | Universal UI pattern |
| 16 | `maispace_teaser` | components | Teaser / preview card linking to another page | ✅ Theme | Standard component for content previews |
| 17 | `maispace_featurebox` | components | Icon/image + heading + text feature highlight | ✅ Theme | Common section on landing pages |
| 18 | `maispace_profile` | components | Person profile card (photo + name + bio) | ✅ Theme | Team / about-us pages are part of most websites |
| 19 | `maispace_testimonial` | components | Customer quote with attribution + avatar | ✅ Theme | Widely used social-proof pattern |
| 20 | `maispace_quote` | components | Styled block-quote with attribution | ✅ Theme | Common editorial element |
| 21 | `maispace_logo` | components | Single logo image with optional link | ✅ Theme | Used in partner / sponsor sections |
| 22 | `maispace_logoshowcase` | components | Logo carousel / grid | ✅ Theme | Common partner / client showcase pattern |
| 23 | `maispace_statistic` | components | Key-figure counter (number + label) | ✅ Theme | Frequently used in about / landing pages |
| 24 | `maispace_pricebox` | components | Pricing tier card (features + price + CTA) | ⚠️ Extension | Pricing is a domain concern — better in a shop / SaaS extension |
| 25 | `maispace_badge` | components | Small label / tag element | ✅ Theme | Generic UI micro-component |
| 26 | `maispace_event` | components | Event listing (date, title, location) | ⚠️ Extension | Events need dedicated records, calendar views, and registration — belongs in an event extension (e.g. `mai_events`) |
| 27 | `maispace_jobposting` | components | Job posting card | ⚠️ Extension | Job boards require structured data, application workflows, and listings — belongs in a recruitment extension (e.g. `mai_jobs`) |

## UI Widgets

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 28 | `maispace_slider` | widgets | Image / content carousel | ✅ Theme | Common interactive component across all website types |
| 29 | `maispace_accordion` | widgets | Collapsible content panels | ✅ Theme | Standard UI widget for FAQs, details, etc. |
| 30 | `maispace_tabs` | widgets | Tabbed content panels | ✅ Theme | Standard UI widget for grouped content |
| 31 | `maispace_modal` | widgets | Overlay / lightbox dialog | ✅ Theme | Generic UI widget, content is static HTML |
| 32 | `maispace_timeline` | widgets | Chronological event list | ✅ Theme | Useful for about-us / history pages — generic enough |
| 33 | `maispace_faq` | widgets | FAQ list (question + answer pairs) | ✅ Theme | Very common on informational websites; structurally similar to accordion |

## Forms & Engagement

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 34 | `maispace_form` | forms | Generic TYPO3 form framework integration | ✅ Theme | Thin wrapper around EXT:form — theming concern |
| 35 | `maispace_search` | forms | On-page search box | ⚠️ Extension | Search requires an indexer, result rendering, and query logic — belongs in a search extension (e.g. EXT:solr, EXT:ke_search) |
| 36 | `maispace_newsletter` | forms | Newsletter signup form | ⚠️ Extension | Needs a mailing-list provider integration, double-opt-in, and subscriber management — belongs in a newsletter extension |
| 37 | `maispace_map` | forms | Embedded map (address / coordinates) | ⚠️ Extension | Requires a map provider API (Google Maps, OpenStreetMap), API keys, and privacy consent — belongs in a maps extension |

## Data & Media

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 38 | `maispace_table` | data | Data table (textTable rendering) | ✅ Theme | Standard content type for tabular data |
| 39 | `maispace_datalist` | data | Definition list / key-value pairs | ✅ Theme | Basic HTML structure, theme-level concern |
| 40 | `maispace_gallery` | data | Image gallery / lightbox grid | ✅ Theme | Common media presentation pattern |
| 41 | `maispace_filelist` | data | Download list of file assets | ✅ Theme | Standard content type for document downloads |
| 42 | `maispace_chart` | data | Data visualisation chart | ⚠️ Extension | Charts need a JS charting library, data-source management, and chart-type configuration — belongs in a data-visualisation extension |
| 43 | `maispace_codeblock` | data | Syntax-highlighted code snippet | ✅ Theme | Useful for documentation / tech sites; purely presentational |
| 44 | `maispace_progressbar` | data | Progress / percentage bar | ❌ Controller | A progress bar reflects dynamic application state (upload progress, form completion, course progress). As a static content element it has very limited value — typically driven by a controller or JavaScript runtime |
| 45 | `maispace_rating` | data | Star / score rating display | ⚠️ Extension | Ratings need user-submitted scores, aggregation, and persistence — belongs in a reviews / rating extension |

## Feedback & State

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 46 | `maispace_alert` | feedback | Inline alert / info box (success, warning, danger, info) | ✅ Theme | Standard UI pattern for editorial notices |
| 47 | `maispace_notification` | feedback | Toast / notification banner | ❌ Controller | Notifications are transient and context-dependent (flash messages after form submission, system alerts). They should be triggered by a controller or middleware, not placed as static content elements |
| 48 | `maispace_spinner` | feedback | Loading spinner / skeleton placeholder | ❌ Controller | Spinners indicate asynchronous loading state driven by JavaScript or a controller — placing one as a static content element makes no practical sense |
| 49 | `maispace_emptystate` | feedback | "No results" / empty placeholder | ❌ Controller | Empty states are conditional UI rendered when a data source returns nothing. This decision belongs in a controller or Fluid partial, not as a standalone CType |
| 50 | `maispace_confirmation` | feedback | Action confirmation dialog | ❌ Controller | Confirmations are responses to user actions (delete, submit, purchase). They require controller logic to trigger and resolve — not suitable as a content element |

## Sections & Layouts

| # | CType | Group | Description | Status | Rationale |
|---|-------|-------|-------------|--------|-----------|
| 51 | `maispace_section_full` | sections | Full-width single-column container | ✅ Theme | Core layout building block (b13/container) |
| 52 | `maispace_section_50_50` | sections | Two equal columns (50 / 50) | ✅ Theme | Core layout building block |
| 53 | `maispace_section_66_33` | sections | Two columns (⅔ + ⅓) | ✅ Theme | Core layout building block |
| 54 | `maispace_section_33_66` | sections | Two columns (⅓ + ⅔) | ✅ Theme | Core layout building block |
| 55 | `maispace_section_3col` | sections | Three equal columns | ✅ Theme | Core layout building block |
| 56 | `maispace_section_4col` | sections | Four equal columns | ✅ Theme | Core layout building block |
| 57 | `maispace_section_sidebar_r` | sections | Main content + right sidebar | ✅ Theme | Core layout building block |
| 58 | `maispace_section_sidebar_l` | sections | Left sidebar + main content | ✅ Theme | Core layout building block |

## Suggested Missing Elements

| # | Suggested CType | Group | Description | Status | Rationale |
|---|-----------------|-------|-------------|--------|-----------|
| 59 | `maispace_breadcrumb` | basic | Breadcrumb navigation trail | 🆕 Missing | Standard navigational aid expected in most themes |
| 60 | `maispace_socialmedia` | basic | Social media icon links | 🆕 Missing | Very common footer / header element for linking to social profiles |
| 61 | `maispace_embed` | basic | Generic oEmbed / iframe wrapper (YouTube, Vimeo, etc.) | 🆕 Missing | Covers third-party embeds with privacy-consent wrapper |
| 62 | `maispace_spacer` | basic | Vertical whitespace / spacing block | 🆕 Missing | Simple layout helper complementing `maispace_divider` |
| 63 | `maispace_callout` | basic | Editorial callout / notice box (note, tip, important) | 🆕 Missing | Common content-aside pattern for highlighting key information in articles |
| 64 | `maispace_iconlist` | basic | List with a custom icon per item | 🆕 Missing | Very common pattern for feature lists, checklists, and benefit summaries |
| 65 | `maispace_textcolumns` | basic | Multi-column text flow (CSS columns) | 🆕 Missing | Purely presentational layout for long-form text without needing section containers |
| 66 | `maispace_contactinfo` | components | Structured contact block (address, phone, email, hours) | 🆕 Missing | Nearly every business website needs a static contact details component |
| 67 | `maispace_beforeafter` | widgets | Before / after image comparison slider | 🆕 Missing | Purely presentational comparison widget — common in portfolios, case studies |
| 68 | `maispace_steps` | widgets | Step indicator / process visualisation | 🆕 Missing | Useful for how-to guides, onboarding flows, and process explanations |

---

## Summary

| Status | Count | Elements |
|--------|-------|----------|
| ✅ Theme | 45 | text, heading, richtext, image, video, audio, button, linklist, icon, divider, hero, banner, cta, mediatext, card, teaser, featurebox, profile, testimonial, quote, logo, logoshowcase, statistic, badge, slider, accordion, tabs, modal, timeline, faq, form, table, datalist, gallery, filelist, codeblock, alert, section_full, section_50_50, section_66_33, section_33_66, section_3col, section_4col, section_sidebar_r, section_sidebar_l |
| ⚠️ Extension | 8 | pricebox, event, jobposting, search, newsletter, map, chart, rating |
| ❌ Controller | 5 | progressbar, notification, spinner, emptystate, confirmation |
| 🆕 Missing | 10 | breadcrumb, socialmedia, embed, spacer, callout, iconlist, textcolumns, contactinfo, beforeafter, steps |
