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
 */

'use strict';

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
// Mobile hamburger toggle (unchanged behaviour, adapted to disclosure nav)
// ---------------------------------------------------------------------------
class MobileToggle {
  /**
   * @param {HTMLButtonElement} button  - .mai-menubar-toggle
   * @param {HTMLElement}       nav     - .mai-menubar-nav
   */
  constructor(button, nav) {
    this.button = button;
    this.nav    = nav;

    this._boundOnClick = this.onClick.bind(this);
    this._boundOnDocumentPointerdown = this.onDocumentPointerdown.bind(this);

    button.addEventListener('click', this._boundOnClick);
    document.addEventListener('pointerdown', this._boundOnDocumentPointerdown, true);
  }

  destroy() {
    this.button.removeEventListener('click', this._boundOnClick);
    document.removeEventListener('pointerdown', this._boundOnDocumentPointerdown, true);
    this.button = null;
    this.nav = null;
  }

  isOpen() {
    return this.button.getAttribute('aria-expanded') === 'true';
  }

  open() {
    // Mobile only: CSS hides .mai-menubar-nav by default below the md breakpoint
    // and shows it when the hamburger button has aria-expanded="true"
    // (sibling selector in menubar-navigation.scss).
    this.button.setAttribute('aria-expanded', 'true');
  }

  close() {
    this.button.setAttribute('aria-expanded', 'false');
  }

  onClick() {
    this.isOpen() ? this.close() : this.open();
  }

  onDocumentPointerdown(event) {
    if (!this.isOpen()) return;
    if (this.button.contains(event.target) || this.nav.contains(event.target)) return;
    this.close();
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
    const nav = toggle.nextElementSibling;
    if (nav && nav.classList.contains('mai-menubar-nav')) {
      toggle.__maiMobileToggle = new MobileToggle(toggle, nav);
    }
  });
});
