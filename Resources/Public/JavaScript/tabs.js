/**
 * tabs.js
 *
 * Accessible tabs component for ext:mai_theme.
 *
 * Follows the W3C WAI-ARIA APG "Tabs with Manual Activation" pattern:
 *   https://www.w3.org/WAI/ARIA/apg/patterns/tabs/
 *
 * The markup rendered by Resources/Private/Partials/Organism/Tabs.html
 * provides a role="tablist" with role="tab" buttons and role="tabpanel"
 * sections. This module wires up:
 *   - Click to activate a tab panel
 *   - Arrow Left / Arrow Right to move focus between tabs (roving tabindex)
 *   - Home / End to jump to first / last tab
 */

'use strict';

// ---------------------------------------------------------------------------
// Single tabs component controller
// ---------------------------------------------------------------------------
class TabsController {
  /**
   * @param {HTMLElement} root - .mai-tabs container
   */
  constructor(root) {
    this.root  = root;
    this.tablist = root.querySelector('[role="tablist"]');

    if (!this.tablist) return;

    this.tabs    = Array.from(this.tablist.querySelectorAll('[role="tab"]'));
    this.panels  = Array.from(root.querySelectorAll('[role="tabpanel"]'));

    this._bindEvents();
  }

  // ---------------------------------------------------------------------------
  // Internal helpers
  // ---------------------------------------------------------------------------

  _bindEvents() {
    this.tabs.forEach((tab, index) => {
      tab.addEventListener('click', (e) => {
        e.preventDefault();
        this._activateTab(index);
      });

      tab.addEventListener('keydown', (e) => {
        let handled = false;

        switch (e.key) {
          case 'ArrowLeft':
          case 'Left':
            this._moveFocusToTab(index - 1);
            handled = true;
            break;

          case 'ArrowRight':
          case 'Right':
            this._moveFocusToTab(index + 1);
            handled = true;
            break;

          case 'Home':
            this._moveFocusToTab(0);
            handled = true;
            break;

          case 'End':
            this._moveFocusToTab(this.tabs.length - 1);
            handled = true;
            break;

          default:
            break;
        }

        if (handled) {
          e.preventDefault();
        }
      });
    });
  }

  /**
   * Activate the tab at the given index.
   * @param {number} index
   */
  _activateTab(index) {
    if (index < 0 || index >= this.tabs.length) return;

    // Deactivate all tabs and hide all panels
    this.tabs.forEach(tab => {
      tab.setAttribute('aria-selected', 'false');
      tab.setAttribute('tabindex', '-1');
    });

    this.panels.forEach(panel => {
      panel.hidden = true;
    });

    // Activate the target tab and show its panel
    const activeTab = this.tabs[index];
    activeTab.setAttribute('aria-selected', 'true');
    activeTab.setAttribute('tabindex', '0');
    activeTab.focus();

    // Show the controlled panel
    const panelId = activeTab.getAttribute('aria-controls');
    if (panelId) {
      const panel = document.getElementById(panelId);
      if (panel) {
        panel.hidden = false;
      }
    }
  }

  /**
   * Move focus to tab at target index (roving tabindex).
   * @param {number} targetIndex
   */
  _moveFocusToTab(targetIndex) {
    if (targetIndex < 0 || targetIndex >= this.tabs.length) return;

    const activeTab = this.tabs[targetIndex];
    activeTab.focus();
  }
}

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.mai-tabs').forEach(root => {
    new TabsController(root);
  });
});
