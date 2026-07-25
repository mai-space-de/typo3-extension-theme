/**
 * language-menu.js
 *
 * Accessible language-switcher dropdown for ext:mai_theme.
 *
 * The markup rendered by Resources/Private/Partials/Organism/Navigation/
 * LanguageMenu.html follows the W3C WAI-ARIA APG "Disclosure" pattern:
 *   https://www.w3.org/WAI/ARIA/apg/patterns/disclosure/
 *
 * A <button class="mai-language-menu__toggle"> (globe + current language)
 * toggles the adjacent <ul class="mai-language-menu__list"> via
 * aria-expanded and the hidden attribute.
 *
 * This module wires up:
 *   - Click to open/close the disclosure button
 *   - Escape closes the panel and returns focus to the button
 *   - Click outside the menu closes the panel
 */

'use strict';

class LanguageMenuDisclosure {
  /**
   * @param {HTMLElement} root - nav.mai-language-menu
   */
  constructor(root) {
    this.root   = root;
    this.button = root.querySelector('.mai-language-menu__toggle');

    // Prefer the list inside this root — the partial is rendered twice
    // (topbar + offcanvas) and duplicate getElementById lookups would always
    // bind every toggle to the first (often display:none) panel.
    this.panel = root.querySelector('.mai-language-menu__list');
    if (!this.panel) {
      const controlledId = this.button ? this.button.getAttribute('aria-controls') : null;
      this.panel = controlledId ? document.getElementById(controlledId) : null;
    }

    if (!this.button || !this.panel) {
      return;
    }

    this._boundOnClick = this.onClick.bind(this);
    this._boundOnKeydown = this.onKeydown.bind(this);
    this._boundOnDocumentPointerdown = this.onDocumentPointerdown.bind(this);

    this.button.addEventListener('click', this._boundOnClick);
    this.root.addEventListener('keydown', this._boundOnKeydown);
    document.addEventListener('pointerdown', this._boundOnDocumentPointerdown, true);
  }

  /**
   * Remove all listeners registered by this instance. Call before
   * re-initialising (e.g. after AJAX content replacement).
   */
  destroy() {
    if (!this.button || !this.panel) {
      return;
    }
    this.button.removeEventListener('click', this._boundOnClick);
    this.root.removeEventListener('keydown', this._boundOnKeydown);
    document.removeEventListener('pointerdown', this._boundOnDocumentPointerdown, true);
    this.root = null;
    this.button = null;
    this.panel = null;
  }

  isOpen() {
    return this.button.getAttribute('aria-expanded') === 'true';
  }

  open() {
    this.button.setAttribute('aria-expanded', 'true');
    this.panel.hidden = false;
  }

  close() {
    this.button.setAttribute('aria-expanded', 'false');
    this.panel.hidden = true;
  }

  onClick() {
    this.isOpen() ? this.close() : this.open();
  }

  onKeydown(event) {
    if (event.key !== 'Escape' && event.key !== 'Esc') return;
    if (!this.isOpen()) return;
    this.close();
    this.button.focus();
    event.preventDefault();
    event.stopPropagation();
  }

  onDocumentPointerdown(event) {
    if (!this.isOpen()) return;
    if (this.root.contains(event.target)) return;
    this.close();
  }
}

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.mai-language-menu').forEach(menu => {
    if (menu.__maiLanguageMenu) {
      menu.__maiLanguageMenu.destroy();
    }
    menu.__maiLanguageMenu = new LanguageMenuDisclosure(menu);
  });
});
