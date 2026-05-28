/**
 * interactive-components.js
 *
 * Architecture reference for interactive component controllers in ext:mai_theme.
 *
 * Individual component JS files (tabs.js, slider.js, modal.js, menubar-navigation.js)
 * are loaded PER TEMPLATE via the mai:asset.js ViewHelper in their respective Fluid
 * templates. The mai:asset system deduplicates by identifier — loading the same
 * component multiple times on a page is safe.
 *
 * Each controller self-initialises on DOMContentLoaded and queries for its own
 * CSS selector:
 *
 *   Controller             Selector            Template
 *   ─────────────────────────────────────────────────────────
 *   TabsController         .mai-tabs           Partials/Organism/Tabs.html
 *   SliderController       .mai-slider         Partials/Organism/Slider.html
 *   ModalController        .mai-modal-trigger  Templates/ContentElements/Modal.html
 *   DisclosureNavigation   .mai-menubar-nav    Partials/Organism/Navigation/Menubar.html
 *   MobileToggle           .mai-menubar-toggle Partials/Organism/Navigation/Menubar.html
 *
 * Re-initialisation safety:
 * Each controller attaches listeners to DOM elements it owns (not global scope,
 * except for disclosure navigation close-on-outside-click). DisclosureNavigation
 * and MobileToggle expose destroy() methods that remove document-level listeners.
 * If content is replaced asynchronously (e.g., AJAX search results), call destroy()
 * on existing instances before re-initialising — this prevents listener stacking
 * when .bind(this) creates new function references.
 *
 * Do NOT import or require this file in production bundles. It exists as a
 * living documentation reference for the component JS architecture.
 */
