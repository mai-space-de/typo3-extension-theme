/**
 * menubar-navigation.js
 *
 * Accessible disclosure navigation for ext:mai_theme.
 *
 * The markup rendered by Resources/Private/Partials/Organism/Navigation/
 * MenubarItem.html follows the W3C WAI-ARIA APG "Disclosure Navigation Menu"
 * pattern:
 *   https://www.w3.org/WAI/ARIA/apg/patterns/disclosure/examples/disclosure-navigation/
 *
 * Every menu entry is a plain <a href> link. When the entry has children,
 * a sibling <button class="mai-menubar__toggle"> carries the chevron and
 * toggles the adjacent <ul class="mai-menubar__submenu"> via aria-expanded
 * and the hidden attribute.
 *
 * This module wires up:
 *   - Click to open/close each disclosure button
 *   - Escape closes the current submenu and returns focus to its button
 *   - Click outside the nav closes all open submenus
 *   - Hover on desktop opens a top-level submenu (and closes siblings)
 *   - Mobile hamburger toggle (.mai-menubar-toggle) opens/closes the whole nav
 *   - Submenu positioning, viewport-collision aware (see positionSubmenu()
 *     below): measures the trigger + submenu against the viewport on open
 *     and flips the panel via CSS utility classes when it would overflow.
 *
 *     (Native CSS anchor positioning was evaluated as a lighter-weight
 *     alternative but dropped — see menubar-navigation.scss for why it
 *     doesn't work with this markup's trigger/submenu nesting.)
 */

'use strict';

/**
 * Collision-detection for submenu placement: measures the trigger and the
 * (now-visible) submenu against the viewport and flips it via utility
 * classes when it would overflow.
 *
 * @param {DisclosureToggle} toggle
 */
function positionSubmenu(toggle) {
  const { submenu, depth } = toggle;
  const li = toggle.button.closest('.mai-menubar__item');
  if (!submenu || !li) return;

  // Reset before measuring so a previous flip doesn't skew this one
  submenu.classList.remove('mai-menubar__submenu--flip-inline', 'mai-menubar__submenu--flip-block');

  const liRect      = li.getBoundingClientRect();
  const menuRect     = submenu.getBoundingClientRect();
  const viewportWidth  = document.documentElement.clientWidth;
  const viewportHeight = document.documentElement.clientHeight;
  // toggle.depth is the depth of the ITEM that owns this button (0 = top
  // level); its submenu is one level deeper. Depth 0 controls the depth-1
  // dropdown (opens below, arrow-down); depth ≥ 1 controls a depth-2+
  // fly-out (opens to the side, arrow-right — see MenubarItem.html).
  const isNested = depth >= 1;

  // Inline axis: top-level dropdowns default to left-aligned under the
  // trigger; nested fly-outs default to the inline-end side of the trigger.
  const inlineEdge = isNested ? liRect.right : liRect.left;
  if (inlineEdge + menuRect.width > viewportWidth) {
    submenu.classList.add('mai-menubar__submenu--flip-inline');
  }

  // Block axis: flip a top-level dropdown above its trigger if it would run
  // past the bottom of the viewport. Nested fly-outs stay pinned to the
  // trigger's top edge — flipping them vertically as well would require
  // recomputing against the (already-flipped) parent panel's edge.
  if (!isNested && liRect.bottom + menuRect.height > viewportHeight) {
    submenu.classList.add('mai-menubar__submenu--flip-block');
  }
}

// ---------------------------------------------------------------------------
// Single disclosure button controller
// ---------------------------------------------------------------------------
class DisclosureToggle {
  /**
   * @param {HTMLButtonElement} button
   * @param {DisclosureNavigation} nav
   */
  constructor(button, nav) {
    this.button = button;
    this.nav    = nav;

    const controlledId = button.getAttribute('aria-controls');
    this.submenu = controlledId ? document.getElementById(controlledId) : null;

    // Depth is encoded in a BEM modifier, e.g. mai-menubar__toggle--depth-0
    this.depth = Number((button.className.match(/--depth-(\d+)/) || [])[1] || 0);

    button.addEventListener('click', this.onClick.bind(this));
    button.addEventListener('keydown', this.onKeydown.bind(this));
  }

  isOpen() {
    return this.button.getAttribute('aria-expanded') === 'true';
  }

  open() {
    if (!this.submenu) return;
    this.button.setAttribute('aria-expanded', 'true');
    this.submenu.hidden = false;
    // Measuring only makes sense once [hidden] is removed and the panel
    // has box dimensions to read.
    positionSubmenu(this);
  }

  close() {
    if (!this.submenu) return;
    this.button.setAttribute('aria-expanded', 'false');
    this.submenu.hidden = true;
  }

  toggle() {
    this.isOpen() ? this.close() : this.open();
  }

  onClick(event) {
    event.preventDefault();
    event.stopPropagation();

    if (this.isOpen()) {
      this.close();
      return;
    }

    // Close sibling disclosures at the same depth within the same parent scope
    this.nav.closeSiblings(this);
    this.open();
  }

  onKeydown(event) {
    switch (event.key) {
      case 'Escape':
      case 'Esc':
        if (this.isOpen()) {
          this.close();
          this.button.focus();
          event.preventDefault();
          event.stopPropagation();
        }
        break;
      case 'ArrowDown':
      case 'Down':
        if (!this.isOpen()) this.open();
        this.focusFirstItemInSubmenu();
        event.preventDefault();
        break;
      default:
        break;
    }
  }

  focusFirstItemInSubmenu() {
    if (!this.submenu) return;
    const first = this.submenu.querySelector('a, button');
    if (first) first.focus();
  }
}

// ---------------------------------------------------------------------------
// Navigation root — collects all toggles and handles global behaviour
// ---------------------------------------------------------------------------
class DisclosureNavigation {
  /**
   * @param {HTMLElement} root - .mai-menubar-nav
   */
  constructor(root) {
    this.root    = root;
    this.toggles = [];
    this._listenerHoverItems = [];

    this._boundOnRootKeydown = this.onRootKeydown.bind(this);
    this._boundOnDocumentPointerdown = this.onDocumentPointerdown.bind(this);
    this._boundOnTopItemPointerenter = this.onTopItemPointerenter.bind(this);
    this._boundOnTopItemPointerleave = this.onTopItemPointerleave.bind(this);

    root.querySelectorAll('.mai-menubar__toggle').forEach(btn => {
      this.toggles.push(new DisclosureToggle(btn, this));
    });

    // Close everything on Escape anywhere inside the nav
    root.addEventListener('keydown', this._boundOnRootKeydown);

    // Close on outside click
    document.addEventListener('pointerdown', this._boundOnDocumentPointerdown, true);

    // Hover behaviour for top-level items on pointer-capable devices
    const supportsHover = window.matchMedia && window.matchMedia('(hover: hover)').matches;
    if (supportsHover) {
      root.querySelectorAll('.mai-menubar > .mai-menubar__item.has-submenu').forEach(li => {
        li.addEventListener('pointerenter', this._boundOnTopItemPointerenter);
        li.addEventListener('pointerleave', this._boundOnTopItemPointerleave);
        this._listenerHoverItems.push(li);
      });
    }
  }

  /**
   * Remove all DOM and document event listeners registered by this instance.
   * Call before re-initialising the navigation (e.g. after AJAX content
   * replacement) to prevent duplicate listener stacking.
   */
  destroy() {
    this.root.removeEventListener('keydown', this._boundOnRootKeydown);
    document.removeEventListener('pointerdown', this._boundOnDocumentPointerdown, true);

    this._listenerHoverItems.forEach(li => {
      li.removeEventListener('pointerenter', this._boundOnTopItemPointerenter);
      li.removeEventListener('pointerleave', this._boundOnTopItemPointerleave);
    });
    this._listenerHoverItems = [];

    this.toggles = [];
    this.root = null;
  }

  closeAll() {
    this.toggles.forEach(t => t.close());
  }

  closeSiblings(activeToggle) {
    // Close any other open toggle whose submenu does NOT contain this button
    this.toggles.forEach(t => {
      if (t === activeToggle) return;
      if (!t.isOpen()) return;
      if (t.submenu && t.submenu.contains(activeToggle.button)) return;
      t.close();
    });
  }

  toggleFor(button) {
    return this.toggles.find(t => t.button === button) || null;
  }

  onRootKeydown(event) {
    if (event.key !== 'Escape' && event.key !== 'Esc') return;
    // Find the closest open toggle and close it
    const openToggles = this.toggles.filter(t => t.isOpen());
    if (openToggles.length === 0) return;
    const last = openToggles[openToggles.length - 1];
    last.close();
    last.button.focus();
    event.preventDefault();
    event.stopPropagation();
  }

  onDocumentPointerdown(event) {
    if (!this.root.contains(event.target)) this.closeAll();
  }

  onTopItemPointerenter(event) {
    const li = event.currentTarget;
    const btn = li.querySelector(':scope > .mai-menubar__toggle');
    const toggle = btn ? this.toggleFor(btn) : null;
    if (!toggle) return;
    this.closeSiblings(toggle);
    toggle.open();
  }

  onTopItemPointerleave(event) {
    const li = event.currentTarget;
    const btn = li.querySelector(':scope > .mai-menubar__toggle');
    const toggle = btn ? this.toggleFor(btn) : null;
    if (toggle) toggle.close();
  }
}

// ---------------------------------------------------------------------------
// Offcanvas hamburger toggle — full-screen panel with focus management.
//
// Accessibility:
//   - aria-expanded on the button reflects panel state.
//   - aria-modal="true" on the nav signals to AT that content outside the
//     panel is inert (backed up by the inert attribute on siblings).
//   - Focus is trapped inside the panel while it is open (Tab cycles within;
//     inert on all siblings prevents AT escape without inert polyfill).
//   - Escape closes the panel and returns focus to the toggle button.
//   - Body scroll is locked while the panel is open.
// ---------------------------------------------------------------------------
class MobileToggle {
  /**
   * @param {HTMLButtonElement} button  - .mai-menubar-toggle
   * @param {HTMLElement}       nav     - .mai-menubar-nav (offcanvas panel)
   */
  constructor(button, nav) {
    this.button       = button;
    this.nav          = nav;
    this._inertedEls  = [];

    this._boundOnClick    = this.onClick.bind(this);
    this._boundOnKeydown  = this.onKeydown.bind(this);
    this._boundTrapFocus  = this.trapFocus.bind(this);

    button.addEventListener('click', this._boundOnClick);
    document.addEventListener('keydown', this._boundOnKeydown);

    // Wire the close button that lives inside the panel
    const closeBtn = nav.querySelector('.mai-menubar-close__btn');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => this.close());
    }
  }

  destroy() {
    this.button.removeEventListener('click', this._boundOnClick);
    document.removeEventListener('keydown', this._boundOnKeydown);
    document.removeEventListener('keydown', this._boundTrapFocus);
    this._releaseInert();
    this.button = null;
    this.nav    = null;
  }

  isOpen() {
    return this.button.getAttribute('aria-expanded') === 'true';
  }

  open() {
    this.button.setAttribute('aria-expanded', 'true');
    this.nav.setAttribute('aria-modal', 'true');
    document.body.classList.add('mai-offcanvas-open');
    this._applyInert();
    document.addEventListener('keydown', this._boundTrapFocus);

    // Move focus into the panel — first to the close button, which is the
    // first interactive element and gives the most predictable entry point.
    const first = this.nav.querySelector(
      '.mai-menubar-close__btn, button:not([disabled]), a[href], [tabindex]:not([tabindex="-1"])'
    );
    if (first) first.focus();
  }

  close() {
    this.button.setAttribute('aria-expanded', 'false');
    this.nav.removeAttribute('aria-modal');
    document.body.classList.remove('mai-offcanvas-open');
    this._releaseInert();
    document.removeEventListener('keydown', this._boundTrapFocus);
    // Return focus to the toggle so keyboard users can continue navigating
    this.button.focus();
  }

  onClick() {
    this.isOpen() ? this.close() : this.open();
  }

  onKeydown(event) {
    if (!this.isOpen()) return;
    if (event.key === 'Escape' || event.key === 'Esc') {
      event.preventDefault();
      this.close();
    }
  }

  // Tab focus trap — cycles focus within the panel for browsers that do not
  // fully honour the inert attribute on siblings yet.
  trapFocus(event) {
    if (event.key !== 'Tab') return;
    const focusables = [...this.nav.querySelectorAll(
      'a[href]:not([disabled]), button:not([disabled]), input:not([disabled]), ' +
      'select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
    )].filter(el => !el.closest('[hidden]'));

    if (!focusables.length) { event.preventDefault(); return; }
    const first = focusables[0];
    const last  = focusables[focusables.length - 1];

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  }

  // Set inert on every element outside the offcanvas panel so that keyboard
  // and AT users cannot reach content behind it.
  //
  // Walk from the panel up to <body> and inert *siblings* at each level.
  // Never inert an ancestor of the panel — that would make the panel itself
  // non-interactive (the nav lives inside .site-header__inner → .site-header).
  _applyInert() {
    let current = this.nav;

    while (current && current !== document.body) {
      const parent = current.parentElement;
      if (!parent) break;

      [...parent.children].forEach(el => {
        if (el === current) return;
        if (el.inert) return;
        el.inert = true;
        this._inertedEls.push(el);
      });

      if (parent === document.body) break;
      current = parent;
    }
  }

  _releaseInert() {
    this._inertedEls.forEach(el => { el.inert = false; });
    this._inertedEls = [];
  }
}

// ---------------------------------------------------------------------------
// Adaptive collapse — switches to the mobile layout as soon as the top-level
// items actually wrap, instead of waiting for the $bp-md viewport breakpoint.
//
// Menu-label width depends on the active language (uk/ar run noticeably
// longer than de), so a fixed pixel breakpoint either wraps ugly two-row
// desktop menus well before it kicks in, or has to be so conservative it
// shows the hamburger on ordinary laptop screens. Measuring the real
// rendered wrap and setting [data-nav-mode="compact"] on .site-header
// (consumed by menubar-navigation.scss's nav-collapsed mixin) sidesteps
// that trade-off entirely. Below the $bp-md media query the native CSS
// already applies, so this is skipped there — and still works with JS off.
// ---------------------------------------------------------------------------
const NAV_COLLAPSE_MIN_WIDTH = 768; // keep in sync with $bp-md in tokens/_breakpoints.scss

class NavCompactController {
  /**
   * @param {HTMLElement} header - .site-header
   * The nav may be a direct child or nested inside .site-header__inner — both
   * are supported.  data-nav-mode is always set on .site-header so the CSS
   * [data-nav-mode="compact"] ancestor selector works from any depth.
   */
  constructor(header) {
    this.header = header;
    // The top-level menubar <ul> has class "mai-menubar"; submenu <ul>s use
    // "mai-menubar__submenu".  This query is unique regardless of nesting depth.
    this.list = header.querySelector('ul.mai-menubar');
    if (!this.list) return;

    this._raf = null;
    this._boundCheck = this.check.bind(this);
    this._boundScheduleCheck = this.scheduleCheck.bind(this);

    this.check();

    this._resizeObserver = ('ResizeObserver' in window) ? new ResizeObserver(this._boundScheduleCheck) : null;
    if (this._resizeObserver) {
      this._resizeObserver.observe(header);
    } else {
      window.addEventListener('resize', this._boundScheduleCheck);
    }

    // Web-font swaps can change label widths after the initial check.
    if (document.fonts && document.fonts.ready) {
      document.fonts.ready.then(this._boundCheck);
    }
  }

  scheduleCheck() {
    if (this._raf) cancelAnimationFrame(this._raf);
    this._raf = requestAnimationFrame(this._boundCheck);
  }

  check() {
    if (!window.matchMedia(`(min-width: ${NAV_COLLAPSE_MIN_WIDTH}px)`).matches) {
      // Native small-viewport CSS already applies; nothing for JS to force.
      return;
    }

    // Re-render in row layout before measuring, so a header that was forced
    // compact by an earlier (narrower) check gets a fair chance to un-collapse.
    this.header.removeAttribute('data-nav-mode');

    if (this.isWrapped()) {
      this.header.setAttribute('data-nav-mode', 'compact');
    }
  }

  isWrapped() {
    const items = Array.from(this.list.children);
    if (items.length < 2) return false;
    const firstTop = items[0].offsetTop;
    return items.some(item => item.offsetTop !== firstTop);
  }

  destroy() {
    if (this._resizeObserver) {
      this._resizeObserver.disconnect();
    } else {
      window.removeEventListener('resize', this._boundScheduleCheck);
    }
    if (this._raf) cancelAnimationFrame(this._raf);
    this.header = null;
    this.list = null;
  }
}

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.mai-menubar-nav').forEach(nav => {
    if (nav.__maiDisclosureNav) {
      nav.__maiDisclosureNav.destroy();
    }
    nav.__maiDisclosureNav = new DisclosureNavigation(nav);
  });

  document.querySelectorAll('.mai-menubar-toggle').forEach(toggle => {
    if (toggle.__maiMobileToggle) {
      toggle.__maiMobileToggle.destroy();
    }
    // Prefer aria-controls reference (robust against DOM ordering)
    const navId = toggle.getAttribute('aria-controls');
    const nav   = (navId && document.getElementById(navId))
                  || toggle.nextElementSibling;
    if (nav && nav.classList.contains('mai-menubar-nav')) {
      toggle.__maiMobileToggle = new MobileToggle(toggle, nav);
    }
  });

  document.querySelectorAll('.site-header').forEach(header => {
    if (header.__maiNavCompact) {
      header.__maiNavCompact.destroy();
    }
    if (header.querySelector('.mai-menubar-nav')) {
      header.__maiNavCompact = new NavCompactController(header);
    }
  });

  // Reveal the offcanvas slide/fade transition (see menubar-navigation.scss
  // ":root:not(.mai-nav-ready)") only once the initial compact/no-compact
  // measurement has settled — otherwise NavCompactController's first check,
  // or its post-font-swap recheck, can itself flash the panel on load.
  const revealNavTransitions = () => {
    requestAnimationFrame(() => requestAnimationFrame(() => {
      document.documentElement.classList.add('mai-nav-ready');
    }));
  };
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(revealNavTransitions, revealNavTransitions);
  } else {
    revealNavTransitions();
  }
});
