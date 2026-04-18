/**
 * menubar-navigation.js
 *
 * Accessible menubar navigation — adapted from the W3C WAI-ARIA APG
 * "Navigation Menubar" example.
 * https://www.w3.org/WAI/ARIA/apg/patterns/menubar/
 *
 * Licence: W3C Software and Document License
 * https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
 *
 * Adaptations for ext:mai_theme:
 *   - BEM class names (.mai-menubar, .mai-menubar__*, .mai-menubar-toggle)
 *   - Mobile toggle integrated (aria-expanded on button, aria-hidden on list)
 *   - No content-generator demo code
 *   - ES module style ('use strict' + DOMContentLoaded bootstrap)
 */

'use strict';

// ---------------------------------------------------------------------------
// MobileToggle — controls the hamburger button on narrow viewports
// ---------------------------------------------------------------------------
class MobileToggle {
  /**
   * @param {HTMLButtonElement} button  - .mai-menubar-toggle
   * @param {HTMLElement}       menubar - .mai-menubar (the <nav> element)
   */
  constructor(button, menubar) {
    this.button  = button;
    this.menubar = menubar;
    this.list    = menubar.querySelector('[role="menubar"]');

    this.button.addEventListener('click', this.onToggleClick.bind(this));

    // Close on outside click / tap
    document.addEventListener('pointerdown', this.onDocumentPointerdown.bind(this), true);
  }

  isOpen() {
    return this.button.getAttribute('aria-expanded') === 'true';
  }

  open() {
    this.button.setAttribute('aria-expanded', 'true');
    if (this.list) {
      this.list.removeAttribute('aria-hidden');
    }
  }

  close() {
    this.button.setAttribute('aria-expanded', 'false');
    if (this.list) {
      this.list.setAttribute('aria-hidden', 'true');
    }
  }

  onToggleClick() {
    this.isOpen() ? this.close() : this.open();
  }

  onDocumentPointerdown(event) {
    if (this.isOpen() && !this.menubar.contains(event.target) && !this.button.contains(event.target)) {
      this.close();
    }
  }
}

// ---------------------------------------------------------------------------
// MenubarNavigation — keyboard interaction per WAI-ARIA APG menubar pattern
// ---------------------------------------------------------------------------
class MenubarNavigation {
  /**
   * @param {HTMLElement} domNode - .mai-menubar (the <nav> element)
   */
  constructor(domNode) {
    this.domNode = domNode;

    this.menuitems      = [];
    this.popups         = [];
    this.menuitemGroups = {};
    this.menuOrientation = {};
    this.isPopup        = {};
    this.isPopout       = {};
    this.openPopups     = false;

    this.firstChars    = {};
    this.firstMenuitem = {};
    this.lastMenuitem  = {};

    // Bootstrap menu tree
    const menubarList = domNode.querySelector('[role="menubar"]');
    if (!menubarList) return;
    this.initMenu(menubarList, 0);

    domNode.addEventListener('focusin',  this.onMenubarFocusin.bind(this));
    domNode.addEventListener('focusout', this.onMenubarFocusout.bind(this));

    window.addEventListener('pointerdown', this.onBackgroundPointerdown.bind(this), true);

    // Set first top-level item as tab stop
    const first = menubarList.querySelector('[role="menuitem"]');
    if (first) first.tabIndex = 0;
  }

  // ── Tree initialisation ─────────────────────────────────────────────────

  getMenuitems(domNode, depth) {
    const nodes   = [];
    const initMenu = this.initMenu.bind(this);
    const popups   = this.popups;

    function findMenuitems(node) {
      while (node) {
        const role = (node.getAttribute('role') || '').trim().toLowerCase();

        if (role === 'menu') {
          node.tabIndex = -1;
          initMenu(node, depth + 1);
          node = node.nextElementSibling;
          continue;
        }

        if (role === 'menuitem') {
          if (node.getAttribute('aria-haspopup') === 'true') {
            popups.push(node);
          }
          nodes.push(node);
        }

        // Recurse into children, but skip SVG subtrees
        if (node.firstElementChild && node.firstElementChild.tagName !== 'svg') {
          findMenuitems(node.firstElementChild);
        }

        node = node.nextElementSibling;
      }
    }

    findMenuitems(domNode.firstElementChild);
    return nodes;
  }

  initMenu(menu, depth) {
    const menuId = this.getMenuId(menu);

    this.menuOrientation[menuId] = this.getMenuOrientation(menu);
    this.isPopup[menuId]  = menu.getAttribute('role') === 'menu' && depth === 1;
    this.isPopout[menuId] = menu.getAttribute('role') === 'menu' && depth > 1;

    this.menuitemGroups[menuId] = [];
    this.firstChars[menuId]     = [];
    this.firstMenuitem[menuId]  = null;
    this.lastMenuitem[menuId]   = null;

    const menuitems = this.getMenuitems(menu, depth);

    for (const menuitem of menuitems) {
      const role = (menuitem.getAttribute('role') || '');
      if (!role.includes('menuitem')) continue;

      menuitem.tabIndex = -1;
      this.menuitems.push(menuitem);
      this.menuitemGroups[menuId].push(menuitem);
      this.firstChars[menuId].push(menuitem.textContent.trim().toLowerCase()[0]);

      menuitem.addEventListener('keydown',      this.onKeydown.bind(this));
      menuitem.addEventListener('click',        this.onMenuitemClick.bind(this), { capture: true });
      menuitem.addEventListener('pointerover',  this.onMenuitemPointerover.bind(this));

      if (!this.firstMenuitem[menuId]) {
        if (this.hasPopup(menuitem)) menuitem.tabIndex = 0;
        this.firstMenuitem[menuId] = menuitem;
      }
      this.lastMenuitem[menuId] = menuitem;
    }
  }

  // ── Focus management ────────────────────────────────────────────────────

  setFocusToMenuitem(menuId, newMenuitem) {
    this.closePopupAll(newMenuitem);
    (this.menuitemGroups[menuId] || []).forEach(item => {
      item.tabIndex = item === newMenuitem ? 0 : -1;
    });
    newMenuitem.focus();
  }

  setFocusToFirstMenuitem(menuId) {
    this.setFocusToMenuitem(menuId, this.firstMenuitem[menuId]);
  }

  setFocusToLastMenuitem(menuId) {
    this.setFocusToMenuitem(menuId, this.lastMenuitem[menuId]);
  }

  setFocusToPreviousMenuitem(menuId, current) {
    const group = this.menuitemGroups[menuId];
    const idx   = group.indexOf(current);
    const prev  = idx === 0 ? this.lastMenuitem[menuId] : group[idx - 1];
    this.setFocusToMenuitem(menuId, prev);
    return prev;
  }

  setFocusToNextMenuitem(menuId, current) {
    const group = this.menuitemGroups[menuId];
    const idx   = group.indexOf(current);
    const next  = idx === group.length - 1 ? this.firstMenuitem[menuId] : group[idx + 1];
    this.setFocusToMenuitem(menuId, next);
    return next;
  }

  setFocusByFirstCharacter(menuId, current, char) {
    const chars = this.firstChars[menuId];
    const group = this.menuitemGroups[menuId];
    let start   = group.indexOf(current) + 1;
    if (start >= group.length) start = 0;

    let idx = this.getIndexFirstChars(menuId, start, char.toLowerCase());
    if (idx === -1) idx = this.getIndexFirstChars(menuId, 0, char.toLowerCase());
    if (idx > -1)   this.setFocusToMenuitem(menuId, group[idx]);
  }

  // ── Popup management ────────────────────────────────────────────────────

  openPopup(menuId, menuitem) {
    const popupMenu = menuitem.nextElementSibling;
    if (!popupMenu) return false;

    const rect = menuitem.getBoundingClientRect();

    if (this.isPopup[menuId]) {
      // Fly-out: position to the right of the parent item
      popupMenu.parentNode.style.position = 'relative';
      popupMenu.style.display  = 'block';
      popupMenu.style.position = 'absolute';
      popupMenu.style.left     = rect.width + 10 + 'px';
      popupMenu.style.top      = '0px';
      popupMenu.style.zIndex   = '100';
    } else {
      // Dropdown: position below the top-level item
      popupMenu.style.display  = 'block';
      popupMenu.style.position = 'absolute';
      popupMenu.style.left     = '0px';
      popupMenu.style.top      = rect.height + 8 + 'px';
      popupMenu.style.zIndex   = '100';
    }

    menuitem.setAttribute('aria-expanded', 'true');
    this.setMenubarDataExpanded('true');
    return this.getMenuId(popupMenu);
  }

  closePopup(menuitem) {
    const menuId = this.getMenuId(menuitem);

    if (this.isMenubar(menuId)) {
      if (this.isOpen(menuitem)) {
        menuitem.setAttribute('aria-expanded', 'false');
        menuitem.nextElementSibling.style.display = 'none';
      }
      return menuitem;
    }

    const menu = this.getMenu(menuitem);
    const cmi  = menu.previousElementSibling;
    cmi.setAttribute('aria-expanded', 'false');
    cmi.focus();
    menu.style.display = 'none';
    return cmi;
  }

  closePopout(menuitem) {
    let menuId = this.getMenuId(menuitem);
    let cmi    = menuitem;

    while (this.isPopup[menuId] || this.isPopout[menuId]) {
      const menu = this.getMenu(cmi);
      cmi    = menu.previousElementSibling;
      menuId = this.getMenuId(cmi);
      menu.style.display = 'none';
    }
    cmi.focus();
    return cmi;
  }

  closePopupAll(menuitem) {
    if (typeof menuitem !== 'object') menuitem = false;
    for (const popup of this.popups) {
      if (this.doesNotContain(popup, menuitem) && this.isOpen(popup)) {
        const cmi = popup.nextElementSibling;
        if (cmi) {
          popup.setAttribute('aria-expanded', 'false');
          cmi.style.display = 'none';
        }
      }
    }
  }

  doesNotContain(popup, menuitem) {
    return !menuitem || !popup.nextElementSibling.contains(menuitem);
  }

  // ── Utilities ───────────────────────────────────────────────────────────

  getIndexFirstChars(menuId, startIndex, char) {
    const chars = this.firstChars[menuId];
    for (let i = startIndex; i < chars.length; i++) {
      if (chars[i] === char) return i;
    }
    return -1;
  }

  isPrintableCharacter(str) {
    return str.length === 1 && /\S/.test(str);
  }

  getIdFromAriaLabel(node) {
    const label = node.getAttribute('aria-label') || '';
    return label.trim().toLowerCase().replace(/[\s/]+/g, '-');
  }

  getMenuOrientation(node) {
    const orientation = node.getAttribute('aria-orientation');
    if (orientation) return orientation;
    const role = node.getAttribute('role');
    if (role === 'menubar') return 'horizontal';
    if (role === 'menu')    return 'vertical';
    return null;
  }

  getMenuId(node) {
    let current = node;
    let role    = current ? current.getAttribute('role') : null;
    while (current && role !== 'menu' && role !== 'menubar') {
      current = current.parentNode;
      role    = current ? current.getAttribute('role') : null;
    }
    return current ? role + '-' + this.getIdFromAriaLabel(current) : false;
  }

  getMenu(menuitem) {
    let node = menuitem;
    let role = node ? node.getAttribute('role') : null;
    while (node && role !== 'menu' && role !== 'menubar') {
      node = node.parentNode;
      role = node ? node.getAttribute('role') : null;
    }
    return node;
  }

  isAnyPopupOpen() {
    return this.popups.some(p => p.getAttribute('aria-expanded') === 'true');
  }

  setMenubarDataExpanded(value) {
    this.domNode.setAttribute('data-menubar-item-expanded', value);
  }

  isMenubarDataExpandedTrue() {
    return this.domNode.getAttribute('data-menubar-item-expanded') === 'true';
  }

  hasPopup(menuitem) {
    return menuitem.getAttribute('aria-haspopup') === 'true';
  }

  isOpen(menuitem) {
    return menuitem.getAttribute('aria-expanded') === 'true';
  }

  isMenubar(menuId) {
    return !this.isPopup[menuId] && !this.isPopout[menuId];
  }

  isMenuHorizontal(menuId) {
    return this.menuOrientation[menuId] === 'horizontal';
  }

  hasFocus() {
    return this.domNode.classList.contains('focus');
  }

  // ── Event handlers ──────────────────────────────────────────────────────

  onMenubarFocusin() {
    this.domNode.classList.add('focus');
  }

  onMenubarFocusout() {
    this.domNode.classList.remove('focus');
  }

  onKeydown(event) {
    const tgt    = event.currentTarget;
    const key    = event.key;
    const menuId = this.getMenuId(tgt);
    let flag = false;
    let mi, id, popupMenuId;

    switch (key) {
      case ' ':
      case 'Enter':
        if (this.hasPopup(tgt)) {
          this.openPopups = true;
          popupMenuId = this.openPopup(menuId, tgt);
          this.setFocusToFirstMenuitem(popupMenuId);
        } else {
          this.closePopupAll();
          this.setMenubarDataExpanded('false');
        }
        flag = true;
        break;

      case 'Esc':
      case 'Escape':
        this.openPopups = false;
        this.closePopup(tgt);
        this.setMenubarDataExpanded('false');
        flag = true;
        break;

      case 'Up':
      case 'ArrowUp':
        if (this.isMenuHorizontal(menuId)) {
          if (this.hasPopup(tgt)) {
            this.openPopups  = true;
            popupMenuId      = this.openPopup(menuId, tgt);
            this.setFocusToLastMenuitem(popupMenuId);
          }
        } else {
          this.setFocusToPreviousMenuitem(menuId, tgt);
        }
        flag = true;
        break;

      case 'Down':
      case 'ArrowDown':
        if (this.isMenuHorizontal(menuId)) {
          if (this.hasPopup(tgt)) {
            this.openPopups  = true;
            popupMenuId      = this.openPopup(menuId, tgt);
            this.setFocusToFirstMenuitem(popupMenuId);
          }
        } else {
          this.setFocusToNextMenuitem(menuId, tgt);
        }
        flag = true;
        break;

      case 'Left':
      case 'ArrowLeft':
        if (this.isMenuHorizontal(menuId)) {
          mi = this.setFocusToPreviousMenuitem(menuId, tgt);
          if (this.isAnyPopupOpen() || this.isMenubarDataExpandedTrue()) {
            this.openPopup(menuId, mi);
          }
        } else {
          if (this.isPopout[menuId]) {
            mi = this.closePopup(tgt);
            id = this.getMenuId(mi);
            this.setFocusToMenuitem(id, mi);
          } else {
            mi = this.closePopup(tgt);
            id = this.getMenuId(mi);
            mi = this.setFocusToPreviousMenuitem(id, mi);
            this.openPopup(id, mi);
          }
        }
        flag = true;
        break;

      case 'Right':
      case 'ArrowRight':
        if (this.isMenuHorizontal(menuId)) {
          mi = this.setFocusToNextMenuitem(menuId, tgt);
          if (this.isAnyPopupOpen() || this.isMenubarDataExpandedTrue()) {
            this.openPopup(menuId, mi);
          }
        } else {
          if (this.hasPopup(tgt)) {
            popupMenuId = this.openPopup(menuId, tgt);
            this.setFocusToFirstMenuitem(popupMenuId);
          } else {
            mi = this.closePopout(tgt);
            id = this.getMenuId(mi);
            mi = this.setFocusToNextMenuitem(id, mi);
            this.openPopup(id, mi);
          }
        }
        flag = true;
        break;

      case 'Home':
      case 'PageUp':
        this.setFocusToFirstMenuitem(menuId);
        flag = true;
        break;

      case 'End':
      case 'PageDown':
        this.setFocusToLastMenuitem(menuId);
        flag = true;
        break;

      case 'Tab':
        this.openPopups = false;
        this.setMenubarDataExpanded('false');
        this.closePopup(tgt);
        break;

      default:
        if (this.isPrintableCharacter(key)) {
          this.setFocusByFirstCharacter(menuId, tgt, key);
          flag = true;
        }
        break;
    }

    if (flag) {
      event.stopPropagation();
      event.preventDefault();
    }
  }

  onMenuitemClick(event) {
    const tgt    = event.currentTarget;
    const menuId = this.getMenuId(tgt);

    if (this.hasPopup(tgt)) {
      this.isOpen(tgt) ? this.closePopup(tgt) : (this.closePopupAll(tgt), this.openPopup(menuId, tgt));
    } else {
      this.closePopupAll();
    }

    event.stopPropagation();
    event.preventDefault();
  }

  onMenuitemPointerover(event) {
    const tgt    = event.currentTarget;
    const menuId = this.getMenuId(tgt);

    if (this.hasFocus()) {
      this.setFocusToMenuitem(menuId, tgt);
    }

    if (this.isAnyPopupOpen() || this.hasFocus()) {
      this.closePopupAll(tgt);
      if (this.hasPopup(tgt)) {
        this.openPopup(menuId, tgt);
      }
    }
  }

  onBackgroundPointerdown(event) {
    if (!this.domNode.contains(event.target)) {
      this.closePopupAll();
    }
  }
}

// ---------------------------------------------------------------------------
// Bootstrap — runs after DOM is ready
// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
  // Keyboard menubar on all .mai-menubar-nav elements
  document.querySelectorAll('.mai-menubar-nav').forEach(nav => {
    new MenubarNavigation(nav);
  });

  // Mobile toggle buttons
  document.querySelectorAll('.mai-menubar-toggle').forEach(toggle => {
    const nav = toggle.nextElementSibling;
    if (nav && nav.classList.contains('mai-menubar-nav')) {
      new MobileToggle(toggle, nav);
    }
  });
});
