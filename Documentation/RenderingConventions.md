# mai_theme — Fluid Rendering Conventions

Conventions every content-element template in `Resources/Private/Templates/ContentElements/`
(and the partials they delegate to) must follow. These are contracts with `mai_assets`,
the b13 container layer, and our own anchor / accessibility tooling — breaking them
silently disables features rather than throwing errors, so they're easy to regress.

## 1. Wrapper attributes (mandatory)

The outermost element of every content-element template must carry:

| Attribute | Value | Why |
|-----------|-------|-----|
| `data-ce-uid` | `{data.uid}` | `mai_assets` `AboveFoldObserver.js` queries `[data-ce-uid]` to feed the IntersectionObserver. Without it the element never enters the above-fold report and never gets flagged as critical. |
| `id` | `{data.tx_maitheme_anchor_id}` if set, otherwise `c{data.uid}` | Anchor links + TYPO3's standard `c<uid>` deep links. |
| `class` | `mai-{slug}` | Per-CType styling hook (`mai-heading`, `mai-card`, `mai-section--50-50`, …). |

Canonical opening tag for a stub:

```html
<section class="mai-{slug}" data-ce-uid="{data.uid}"{f:if(condition: data.tx_maitheme_anchor_id, then: ' id="{data.tx_maitheme_anchor_id}"', else: ' id="c{data.uid}"')}>
```

When a template delegates to a partial (e.g. `Accordion.html` → `Organism/Accordion.html`),
the partial is responsible for emitting the wrapper with `data-ce-uid`. The CType
template itself just renders the partial. See `Resources/Private/Partials/Organism/Accordion.html`
for an example.

## 2. Above-fold detection — how the contract is consumed

1. `mai_assets` `AboveFoldObserverListener` injects `AboveFoldObserver.js` before
   `</body>` on every cacheable page.
2. The script runs `document.querySelectorAll('[data-ce-uid]')`. **If nothing
   matches, the script silently exits** and posts an empty report — no critical
   CSS will ever be detected for that page.
3. For each `[data-ce-uid]` element that intersects the viewport on load, the uid
   is recorded and POSTed to `/api/mai-assets/above-fold-report`.
4. The next request to that page can then mark those CTypes as critical via the
   `CriticalAssetDataProcessor` (`tt_content.stdWrap.dataProcessing.100`).

Practical implication: a forgotten `data-ce-uid` doesn't crash — it just makes the
above-fold detection a no-op for that CType. Watch for it in code review.

## 3. Section containers (b13)

`b13/container` does not ship a Fluid ViewHelper. Children are exposed via
`B13\Container\DataProcessing\ContainerProcessor`, which — when called without an
explicit `colPos` — populates one `children_<colPos>` Fluid variable per registered
column.

Wiring (TypoScript, `lib/contentElement.typoscript`):

```typoscript
tt_content.maispace_section_50_50 < lib.contentElement
tt_content.maispace_section_50_50 {
    templateName = Section5050
    dataProcessing.10 = B13\Container\DataProcessing\ContainerProcessor
}
```

Template (`Templates/ContentElements/Section5050.html`):

```html
<section class="mai-section mai-section--50-50" data-ce-uid="{data.uid}" id="c{data.uid}">
    <div class="mai-section__inner">
        <div class="mai-section__col">
            <f:for each="{children_200}" as="child">{child.renderedContent -> f:format.raw()}</f:for>
        </div>
        <div class="mai-section__col">
            <f:for each="{children_201}" as="child">{child.renderedContent -> f:format.raw()}</f:for>
        </div>
    </div>
</section>
```

The `<f:format.raw>` is required — `child.renderedContent` is already the fully
rendered HTML of the child CType.

Do **not** use `<b13:container.renderChildren>`. There is no such ViewHelper; that
namespace doesn't exist in the package.

## 4. Stub templates

Phase-2 stubs under `Templates/ContentElements/` are intentionally minimal:
they render `data.header` (via `Atom/Heading`) and `data.bodytext` inside a
`<section class="mai-{slug}" data-ce-uid="{data.uid}" id="…">`. Replace them
wave-by-wave per `PLANNED_ELEMENTS_IMPLEMENTATION.md` §1 — but keep the wrapper
attributes in §1 above intact when you do.

## 5. Shared TypoScript base

Every `tt_content.maispace_*` block must inherit from `lib.contentElement`:

```typoscript
tt_content.maispace_<type> < lib.contentElement
tt_content.maispace_<type>.templateName = <Type>
```

`lib.contentElement` (defined at the top of `lib/contentElement.typoscript`) carries
the `templateRootPaths` / `partialRootPaths` / `layoutRootPaths` and the `settings.pids`
register. Don't redeclare those per-CType.

## 6. Critical vs. deferred assets per content element

The above-fold pipeline in `mai_assets` already exposes the critical flag to every
CE template — use it from Fluid rather than teaching the ViewHelpers about it.

### How `isCritical` reaches the template

`mai_assets` registers `CriticalAssetDataProcessor` as
`tt_content.stdWrap.dataProcessing.100`. For every CE rendered, it consults
`CriticalDetectionService::isCritical($pageUid, $elementUid)` and decorates the
Fluid variable scope with:

| Variable | Value when critical | Value when not critical |
|----------|--------------------|--------------------------|
| `isCritical` | `true` | `false` |
| `cssStrategy` | `'inline'` | `'deferred'` |
| `loadingStrategy` | `'eager'` | `'lazy'` |
| `fetchPriority` | `'high'` | `'low'` |
| `decodingStrategy` | `'sync'` | `'async'` |

The flag is fed from (in priority order):

1. TCA field `tx_maiassets_force_critical` (editor override — always critical).
2. TCA field `tx_maiassets_is_critical` (editor override — yes/no).
3. The cached report from `AboveFoldObserver.js` (i.e. elements the browser saw
   above the fold on a prior visit, keyed by `data-ce-uid` per bucket).
4. Fallback heuristic: position + `criticalThresholdByColPos` in
   `EXT:mai_assets` extension configuration.

### Pattern in CE templates — inline critical, link deferred

When a CE has component-specific CSS/JS, let the template pick the delivery mode:

```html
<section class="mai-card" data-ce-uid="{data.uid}" id="c{data.uid}">
    <mai:asset.criticalStyle identifier="mai-card-{data.uid}"
                             source="EXT:mai_theme/Resources/Public/Scss/components/card.scss"
                             isCritical="{isCritical}"/>
    <!-- card markup -->
</section>
```

`CriticalStyleViewHelper` branches internally on `isCritical`:

- **`isCritical = true`** → compiles the SCSS, minifies, emits `<style>…</style>`
  at the call site (inline critical).
- **`isCritical = false`** → registers the stylesheet via `AssetCollector` for a
  deduplicated `<link rel="stylesheet">` in `<head>`.

For JS, do the same with `<mai:asset.js>` driven by `loadingStrategy`:

```html
<mai:asset.js identifier="mai-card-{data.uid}"
              src="EXT:mai_theme/Resources/Public/JavaScript/card.js"
              defer="{f:if(condition: isCritical, then: 0, else: 1)}"
              async="{f:if(condition: isCritical, then: 1, else: 0)}"/>
```

### Why this over a new "ask the ViewHelper to decide" API

The ViewHelper doesn't need to know *which* CE it belongs to — the template
already does. Branching in the template keeps the ViewHelper boundary clean
(stylesheet paths in, HTML/AssetCollector out) and lets the same ViewHelper work
from the Page template (where there is no `elementUid`) and from CE templates
(where the data processor has just decided `isCritical` for this element).

### What *not* to do

Don't read the AboveFold cache from a ViewHelper directly. Don't pass
`data.uid` to a ViewHelper so it can re-ask `CriticalDetectionService` — the
data processor already did that work once per CE on every request.

### Known gaps to fix when this pattern gets exercised for real

- `CriticalStyleViewHelper` emits `<style>` at its call site. Inline `<style>`
  inside `<body>` is valid HTML but `<head>` placement is preferable. When we
  start inlining component-specific critical CSS, move the ViewHelper to register
  via `AssetCollector::addInlineStyleSheet` so the collector lifts it into `<head>`.
- The non-critical branch currently registers the raw `.scss` path with the
  collector (same class of bug the global `<mai:asset.css>` used to have — now
  fixed via `CompiledAssetPublisher`). When we wire component stylesheets for
  real, route that branch through `CompiledAssetPublisher` too.
