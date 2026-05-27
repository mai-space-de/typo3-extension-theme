# PLANNED_ELEMENTS — Implementation Plan (ext:mai_theme)

Authoritative, executable plan for rendering the Theme's custom elements in Fluid,
wiring `settings.definitions.yaml → :root { --mai-* }` through TypoScript, and
consuming the Assets ViewHelpers (`packages/typo3-extension-assets/Classes/ViewHelpers`).

Scope anchors:

- TCA: `packages/typo3-extension-theme/Configuration/TCA/` and `TCA/Overrides/`
- Settings: `packages/typo3-extension-theme/Configuration/Sets/MaiTheme/settings.definitions.yaml`
- Fluid: `packages/typo3-extension-theme/Resources/Private/{Templates,Partials,Layouts}/`
- Page layout: `Resources/Private/Templates/Page/Default.html`
- Assets VHs (namespace `mai`):
  - `mai:asset.criticalStyle`, `mai:asset.css`, `mai:asset.js`, `mai:asset.hint`, `mai:asset.preloadFont`
  - `mai:image.picture`, `mai:image.figure`, `mai:image.responsiveImage`
  - `mai:svg.icon`, `mai:svg.inline`
  - `mai:video.video`

---

### 1. TCA element → Fluid template map

Item tables (repeatable children via IRRE/inline):

| TCA table | Parent CType | Item template |
|---|---|---|
| `tx_maitheme_accordion_item` | `maitheme_accordion` | `Partials/Organism/Accordion.html` + `Partials/Items/AccordionItem.html` |
| `tx_maitheme_slider_item` | `maitheme_slider` | `Partials/Organism/Slider.html` + `Partials/Items/SliderItem.html` |
| `tx_maitheme_tab_item` | `maitheme_tabs` | `Partials/Organism/Tabs.html` + `Partials/Items/TabItem.html` |
| `tx_maitheme_timeline_item` | `maitheme_timeline` | `Partials/Organism/Timeline.html` + `Partials/Items/TimelineItem.html` |

`tt_content` overrides (`Configuration/TCA/Overrides/tt_content*.php`) and the
`elements.*` toggles in `settings.definitions.yaml` → one template per CType under
`Resources/Private/Templates/ContentElements/`:

Basic (`elements.basic`): `Text.html`, `Heading.html`, `Richtext.html`, `Image.html`,
`Video.html`, `Audio.html`, `Button.html`, `Linklist.html`, `Icon.html`, `Divider.html`.

Components (`elements.components`): `Card.html`, `Teaser.html`, `Hero.html`, `Banner.html`,
`Featurebox.html`, `Cta.html`, `TextMedia.html` (exists), `Profile.html`, `Testimonial.html`,
`Quote.html`, `Logo.html`, `Logoshowcase.html`, `Statistic.html`, `Pricebox.html`, `Badge.html`.

Widgets (`elements.widgets`): `Slider.html` (exists), `Accordion.html` (exists), `Tab.html`
(exists, rename-align with `Tabs`), `Modal.html`, `Timeline.html` (exists), `Faq.html`, `Map.html`.

Forms (`elements.forms`): `Form.html`, `Search.html`, `Newsletter.html`.

Data (`elements.data`): `Table.html`, `Datalist.html`, `Gallery.html`, `Filelist.html`,
`Codeblock.html`, `Chart.html`, `Progressbar.html`, `Rating.html`, `Event.html`, `Jobposting.html`.

Feedback (`elements.feedback`): `Alert.html`, `Notification.html`, `Spinner.html`,
`Emptystate.html`, `Confirmation.html`.

Sections (`elements.sections`): `SectionContainer.html` (exists), `SectionMultiColumn.html`
(exists), `BentoBox.html` (exists), `BentoCell.html` (exists).

### 2. Shared Partials (exposed via `partialRootPaths`)

```
Resources/Private/Partials/
├── Atom/
│   ├── Heading.html          (existing) — f:format.html + h{level} switch
│   ├── Button.html           (existing) — <a|button>, aria, icon slot
│   ├── Icon.html             (existing) — wraps mai:svg.icon / mai:svg.inline
│   ├── Image.html            (existing) — wraps mai:image.responsiveImage
│   ├── Figure.html           (new)      — wraps mai:image.figure
│   ├── Picture.html          (new)      — wraps mai:image.picture (art direction)
│   ├── Link.html             (new)      — typolink + target/rel/aria
│   ├── Video.html            (new)      — wraps mai:video.video
│   └── Divider.html          (new)
├── Molecule/
│   ├── Card.html             (existing)
│   ├── MediaText.html        (existing)
│   ├── MediaBox.html         (new) — figure/picture + caption + overlay
│   ├── CardWrapper.html      (new) — <article class> + appearance style vars
│   └── SectionWrapper.html   (new) — <section class> + layout style vars
├── Organism/
│   ├── Accordion.html / Slider.html / Tabs.html / Timeline.html (existing)
│   ├── Gallery.html (new), Faq.html (new), Logoshowcase.html (new)
├── Items/                    (new — inline children)
│   ├── AccordionItem.html, TabItem.html, SliderItem.html, TimelineItem.html
│   ├── CardItem.html, GalleryItem.html, LogoItem.html, TestimonialItem.html
│   └── FaqItem.html, EventItem.html
├── Appearance/  Background.html Border.html Shadow.html Opacity.html TextColor.html
├── Layout/      Spacing.html MaxWidth.html Alignment.html DisplayMode.html
├── Animation/   Entrance.html Hover.html Parallax.html
├── Link/        LinkWrap.html AnchorId.html AriaLabel.html
├── Responsive/  Visibility.html Order.html ColumnSpan.html
└── Meta/        CustomClass.html CustomId.html DataAttributes.html Visibility.html
```

TypoScript registration (in `Configuration/Sets/MaiTheme/setup.typoscript`):

```typoscript
lib.contentElement {
    partialRootPaths {
        100 = EXT:mai_theme/Resources/Private/Partials/
        110 = EXT:mai_theme/Resources/Private/Partials/Atom/
        120 = EXT:mai_theme/Resources/Private/Partials/Molecule/
        130 = EXT:mai_theme/Resources/Private/Partials/Organism/
        140 = EXT:mai_theme/Resources/Private/Partials/Items/
    }
    templateRootPaths.100 = EXT:mai_theme/Resources/Private/Templates/ContentElements/
    layoutRootPaths.100   = EXT:mai_theme/Resources/Private/Layouts/
}
page.10.partialRootPaths  < lib.contentElement.partialRootPaths
page.10.layoutRootPaths   < lib.contentElement.layoutRootPaths
page.10.templateRootPaths < lib.contentElement.templateRootPaths
```

### 3. `lib.themeVars` — emit `:root { --mai-* }` from settings

Goal: every `frontend.*` key in `settings.definitions.yaml` becomes one CSS custom
property named `--mai-<dotted-key-kebabed>` on `:root`.

Mapping rule (deterministic):
`frontend.color.primary` → `--mai-color-primary`
`frontend.color.primaryHover` → `--mai-color-primary-hover`
`frontend.button.radius` → `--mai-button-radius`
`frontend.layout.containerMaxWidth` → `--mai-layout-container-max-width`

TypoScript (`setup.typoscript`, excerpt):

```typoscript
lib.themeVars = TEXT
lib.themeVars {
    wrap = :root{|}
    stdWrap.cObject = COA
    stdWrap.cObject {
        10 = TEXT
        10.value = --mai-color-text:{$frontend.color.text};
        10.insertData = 1
        20 = TEXT
        20.value = --mai-color-surface:{$frontend.color.surface};
        20.insertData = 1
        # …one line per frontend.* key (generated/maintained 1:1 with YAML)
        999 = TEXT
        999.value = --mai-font-body:{$frontend.font.bodyFamily};
        999.insertData = 1
    }
}

# Font preload URLs collected from settings
lib.themeFontPreloads = COA
lib.themeFontPreloads {
    10 = TEXT
    10.value = {$frontend.font.bodyUrl}
    10.insertData = 1
    20 = TEXT
    20.value = {$frontend.font.headingUrl}
    20.insertData = 1
}
```

### 4. Injection into `Resources/Private/Templates/Page/Default.html`

```html
{namespace mai=Maispace\MaiAssets\ViewHelpers}
<f:layout name="Default"/>

<f:section name="HeadAssets">
    <mai:asset.preloadFont href="{settings.frontend.font.bodyUrl}"
                          type="font/woff2" crossorigin="anonymous"/>
    <mai:asset.preloadFont href="{settings.frontend.font.headingUrl}"
                          type="font/woff2" crossorigin="anonymous"/>

    <mai:asset.criticalStyle identifier="mai-theme-vars">
        <f:cObject typoscriptObjectPath="lib.themeVars"/>
    </mai:asset.criticalStyle>

    <mai:asset.css identifier="mai-theme"
                   href="EXT:mai_theme/Resources/Public/Css/theme.css"/>
</f:section>

<f:section name="Main">
    <f:render partial="Page/Header" arguments="{_all}"/>
    <main id="main" role="main">
        <f:cObject typoscriptObjectPath="page.10"/>
    </main>
    <f:render partial="Page/Footer" arguments="{_all}"/>
</f:section>
```

TypoScript exposes `settings` to Fluid:

```typoscript
page.10 = FLUIDTEMPLATE
page.10 {
    templateName = Default
    settings < plugin.tx_maitheme.settings
    dataProcessing.10 = TYPO3\CMS\Frontend\DataProcessing\SiteProcessor
}
```

### 5. Semantic wrapper conventions (one row = one rule)

| Element group | Root tag | Notes / child semantics | Assets VHs used |
|---|---|---|---|
| Card, Teaser, Profile, Testimonial, Quote, News item | `<article>` | `<header><h{n}>` + `<footer>` for meta; `<blockquote><cite>` for quote/testimonial | `mai:image.responsiveImage`, `mai:svg.icon` |
| Hero, Banner, CTA, `sections.*`, Featurebox | `<section aria-labelledby="…">` | heading target required; may contain `<picture>` background | `mai:image.picture`, `mai:video.video` |
| Image, Video, Audio, Gallery (single item) | `<figure>` + `<figcaption>` | gallery wrapper `<ul>` of `<li><figure>` | `mai:image.figure`, `mai:image.picture`, `mai:image.responsiveImage`, `mai:video.video` |
| Breadcrumb, Linklist, Socialmedia | `<nav aria-label="…">` → `<ol>`/`<ul>` | breadcrumb uses `<ol>` + `aria-current="page"` | `mai:svg.icon` |
| Callout, Alert, Notification | `<aside role="note">` / `role="alert"` | alert uses `role="alert"` + `aria-live="polite|assertive"` | `mai:svg.icon` |
| Datalist, Definition list | `<dl>` with `<dt>/<dd>` | — | — |
| Accordion, FAQ | `<details>`/`<summary>` (progressive) wrapped in `<section>` | JS enhancement optional | `mai:svg.icon` |
| Steps, Timeline | `<ol>` with `<li>` (+ `aria-posinset`) | timeline entries `<article>` inside `<li>` | `mai:svg.icon`, `mai:image.responsiveImage` |
| Data table | `<table>` + `<caption>` + `<th scope="col\|row">` | `<thead>/<tbody>/<tfoot>` | — |
| Slider | `<section role="region" aria-roledescription="carousel">` → `<ul>` of `<li role="group">` | — | `mai:image.picture`, `mai:svg.inline` |
| Tabs | `<div role="tablist">` + `<button role="tab">` + `<section role="tabpanel">` | — | `mai:svg.icon` |
| Button, Link | `<a>` if `href`, else `<button type>` | icon-only gets `aria-label` | `mai:svg.icon` |
| Icon | inline `<svg>` via `mai:svg.inline`, `<use>` via `mai:svg.icon` | decorative → `aria-hidden="true"` | `mai:svg.inline`, `mai:svg.icon` |
| Divider | `<hr>` | decorative → `role="presentation"` | — |

### 6. Field → CSS variable binding

Theme-level tokens live on `:root` (from `lib.themeVars`). Per-element TCA appearance
fields are applied as scoped overrides using inline `style="--local-*: …"` on the
semantic wrapper; component CSS consumes them via `var(--local-*, var(--mai-*))`.

| Element family | Theme tokens (`:root`) | Per-instance overrides (inline `style`) | Source TCA fields |
|---|---|---|---|
| Button | `--mai-btn-bg`, `--mai-btn-fg`, `--mai-btn-radius`, `--mai-btn-padding`, `--mai-btn-border` | `--local-btn-bg`, `--local-btn-fg`, `--local-btn-radius` | `tx_maitheme_btn_variant`, `…_color`, `…_radius` |
| Image / Figure / Picture | `--mai-img-radius`, `--mai-img-ratio`, `--mai-img-shadow` | `--local-img-radius`, `--local-img-ratio`, `--local-img-fit` | `image_radius`, `image_ratio`, `image_fit` |
| Video / Audio | `--mai-media-radius`, `--mai-media-controls-bg` | `--local-media-radius`, `--local-media-poster-fit` | `media_radius`, `poster_fit` |
| Card / Teaser / Profile | `--mai-card-bg`, `--mai-card-radius`, `--mai-card-shadow`, `--mai-card-padding` | `--local-card-bg`, `--local-card-radius` | `card_bg`, `card_radius`, `card_shadow` |
| Section wrappers (`sections.*`, Hero, Banner, CTA) | `--mai-layout-container`, `--mai-layout-gap`, `--mai-layout-py`, `--mai-layout-px` | `--local-layout-py`, `--local-layout-bg`, `--local-layout-align` | `section_space_before/after`, `section_bg`, `section_align` |
| Heading / Text / Richtext | `--mai-font-body`, `--mai-font-heading`, `--mai-color-text`, `--mai-fs-h1..h6` | `--local-fs`, `--local-color-text`, `--local-align` | `heading_size`, `heading_color`, `text_align` |
| Alert / Notification | `--mai-alert-bg-{variant}`, `--mai-alert-fg-{variant}` | `--local-alert-variant` via class | `alert_variant` |
| Slider / Tabs / Accordion / Timeline | `--mai-component-gap`, `--mai-component-radius`, `--mai-component-indicator` | `--local-component-gap`, `--local-component-indicator` | `component_gap`, `component_indicator_color` |
| Divider | `--mai-divider-color`, `--mai-divider-thickness` | `--local-divider-color`, `--local-divider-style` | `divider_color`, `divider_style` |

Pattern applied in every element template via `Molecule/SectionWrapper.html` /
`Molecule/CardWrapper.html`:

```html
<article class="mai-card {data.tx_maitheme_card_variant}"
         style="{f:if(condition: data.card_bg, then: '--local-card-bg: {data.card_bg};')}{f:if(condition: data.card_radius, then: '--local-card-radius: {data.card_radius}px;')}">
    …
</article>
```

### 7. Final deliverables list

New / updated files:

1. `Resources/Private/Templates/Page/Default.html` — add `HeadAssets` section with
   `mai:asset.preloadFont`, `mai:asset.criticalStyle` emitting `lib.themeVars`, and
   `mai:asset.css` for the compiled theme stylesheet.
2. `Configuration/Sets/MaiTheme/setup.typoscript` — add `lib.themeVars`,
   `lib.themeFontPreloads`, and `partialRootPaths` / `templateRootPaths` registration.
3. One template per CType under `Resources/Private/Templates/ContentElements/` per
   the list in §1 (fill in the missing ones alongside the existing 9).
4. Shared partials per §2 under `Resources/Private/Partials/{Atom,Molecule,Organism,Items,Appearance,Layout,Animation,Link,Responsive,Meta}/`.
5. Key new atom/molecule partials to create first (highest reuse): `Atom/Figure.html`,
   `Atom/Picture.html`, `Atom/Link.html`, `Atom/Video.html`, `Molecule/MediaBox.html`,
   `Molecule/CardWrapper.html`, `Molecule/SectionWrapper.html`.
6. Items partials for the 4 IRRE child tables: `Items/AccordionItem.html`,
   `Items/SliderItem.html`, `Items/TabItem.html`, `Items/TimelineItem.html`.

### 8. Accessibility checklist (validate each element against)

- **Landmarks**: exactly one `<main>`; `<header>`/`<footer>`/`<nav>` present at page
  level; section landmarks (`<section>`, `<aside>`, `<nav>`) carry `aria-label` or
  `aria-labelledby`.
- **Heading hierarchy**: no skipped levels; each `<section>` contains a heading;
  `Atom/Heading.html` enforces `level` 1–6 with default `2`.
- **Images**: `alt` required (empty `alt=""` only when decorative and
  `role="presentation"`); `mai:image.figure` populates `<figcaption>` from TCA.
- **Links & buttons**: `<a>` needs discernible text or `aria-label`; icon-only buttons
  get `aria-label`; external links add `rel="noopener noreferrer"` and visible
  indicator; `target="_blank"` announced via label.
- **Forms**: every input has `<label for>`, error messages via `aria-describedby`,
  `aria-invalid="true"` on error, required via `aria-required`.
- **Tables**: `<caption>` set; `<th scope>` on every header cell; complex tables use
  `headers`/`id` association.
- **Interactive widgets**:
  - Accordion/FAQ: `<details>` (progressive) or `button[aria-expanded][aria-controls]`.
  - Tabs: `role="tablist"`, `role="tab"`, `aria-selected`, `aria-controls`,
    `role="tabpanel"`, `aria-labelledby`, roving `tabindex`.
  - Slider: `role="region"` + `aria-roledescription="carousel"`; controls with
    `aria-label`; `aria-live="polite"` on the live region; respects
    `prefers-reduced-motion`.
  - Modal: `role="dialog"` + `aria-modal="true"` + focus trap + `aria-labelledby`.
  - Alert: `role="alert"` / `aria-live` as appropriate.
- **Color & contrast**: all `--mai-color-*` pairs verified ≥ 4.5:1 (AA) / 3:1 for
  large text and non-text UI; focus indicators ≥ 3:1 against adjacent colors.
- **Focus states**: every interactive element has a visible `:focus-visible`
  outline backed by `--mai-focus-ring` on `:root`; never `outline: none` without
  a replacement.
- **Motion**: wrap entrance/parallax partials in
  `@media (prefers-reduced-motion: reduce)` fallbacks.
- **Language**: `<html lang>` comes from site config; per-block language overrides
  use `lang` attribute on the wrapper.
- **Keyboard**: all widgets operable without a mouse; tab order matches visual
  order; skip-link to `#main` present in `Default.html`.

### 9. Implementation order (execution sequence)

1. Add `lib.themeVars` + `partialRootPaths` in `setup.typoscript`.
2. Update `Templates/Page/Default.html` with the `HeadAssets` injection.
3. Create the new Atom/Molecule partials (Figure, Picture, Link, Video, MediaBox,
   CardWrapper, SectionWrapper).
4. Fill the 4 Items partials for existing Organism templates (Accordion, Slider,
   Tabs, Timeline).
5. Create the remaining ContentElements templates in waves: Basic → Components →
   Widgets → Data → Feedback → Forms.
6. After each wave, run `composer lint:check` inside `packages/typo3-extension-theme`
   (phpcs/phpstan/typoscript-lint) and verify rendering via DDEV.
7. Validate each element against §8 checklist before marking it done.
