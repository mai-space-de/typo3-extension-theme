/**
 * modal.js
 *
 * Accessible modal dialog for ext:mai_theme.
 *
 * Uses the native HTML <dialog> element for automatic focus management,
 * ::backdrop styling, and Escape-key dismissal.
 *
 * The markup rendered by Resources/Private/Templates/ContentElements/Modal.html
 * provides a trigger button, a <dialog> with aria-labelledby, and a close button.
 * This module wires up:
 *   - Click trigger to open dialog (dialog.showModal())
 *   - Click close button to close dialog
 *   - Escape key to close dialog (native <dialog> behaviour)
 *   - Click outside (on ::backdrop) to close dialog
 *   - Focus trap: focus cycles inside the dialog container
 *   - Return focus to the trigger button when dialog closes
 */

'use strict';

// ---------------------------------------------------------------------------
// Single modal controller
// ---------------------------------------------------------------------------
class ModalController {
  /**
   * @param {HTMLElement} root - .mai-modal-trigger container
   */
  constructor(root) {
    this.root    = root;
    this.trigger = root.querySelector('.mai-modal-trigger__btn');
    this.dialog  = root.querySelector('.mai-modal');

    if (!this.trigger || !this.dialog) return;

    this._bindEvents();
  }

  // ---------------------------------------------------------------------------
  // Events
  // ---------------------------------------------------------------------------

  _bindEvents() {
    // Open on trigger click
    this.trigger.addEventListener('click', () => this.open());

    // Close button
    const closeBtn = this.dialog.querySelector('.mai-modal__close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => this.close());
    }

    // Close on backdrop click
    this.dialog.addEventListener('click', (e) => {
      if (e.target === this.dialog) {
        this.close();
      }
    });

    // Close on Escape (native <dialog> behaviour, but also update trigger)
    this.dialog.addEventListener('cancel', (e) => {
      e.preventDefault(); // Prevent default to handle our own close logic
      this.close();
    });

    // Focus trap
    this.dialog.addEventListener('keydown', (e) => this._handleKeydown(e));
  }

  // ---------------------------------------------------------------------------
  // Open / Close
  // ---------------------------------------------------------------------------

  open() {
    this.dialog.showModal();
    this.trigger.setAttribute('aria-expanded', 'true');

    // Focus the first focusable element inside the dialog
    this._focusFirstElement();
  }

  close() {
    this.dialog.close();
    this.trigger.setAttribute('aria-expanded', 'false');

    // Return focus to trigger
    this.trigger.focus();
  }

  // ---------------------------------------------------------------------------
  // Focus management
  // ---------------------------------------------------------------------------

  /**
   * Get all focusable elements within the dialog.
   * @returns {HTMLElement[]}
   */
  _getFocusableElements() {
    const selector = [
      'a[href]',
      'button:not([disabled])',
      'textarea:not([disabled])',
      'input:not([disabled])',
      'select:not([disabled])',
      '[tabindex]:not([tabindex="-1"])'
    ].join(', ');

    return Array.from(this.dialog.querySelectorAll(selector)).filter(el => {
      return !el.closest('[hidden]') && el.offsetParent !== null;
    });
  }

  _focusFirstElement() {
    const focusable = this._getFocusableElements();
    if (focusable.length > 0) {
      focusable[0].focus();
    } else {
      this.dialog.focus();
    }
  }

  _handleKeydown(e) {
    if (e.key !== 'Tab') return;

    const focusable = this._getFocusableElements();
    if (focusable.length === 0) return;

    const first = focusable[0];
    const last  = focusable[focusable.length - 1];

    if (e.shiftKey) {
      // Shift+Tab: if on first element, wrap to last
      if (document.activeElement === first) {
        e.preventDefault();
        last.focus();
      }
    } else {
      // Tab: if on last element, wrap to first
      if (document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  }
}

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.mai-modal-trigger').forEach(root => {
    new ModalController(root);
  });
});
