/**
 * slider.js
 *
 * Accessible carousel / slider component for ext:mai_theme.
 *
 * Follows the W3C WAI-ARIA APG "Carousel" pattern:
 *   https://www.w3.org/WAI/ARIA/apg/patterns/carousel/
 *
 * The markup rendered by Resources/Private/Partials/Organism/Slider.html
 * uses CSS scroll-snap for basic horizontal scrolling. This module
 * enhances it with:
 *   - Prev / Next buttons with aria-label
 *   - Dot indicators with aria-current
 *   - Arrow Left / Arrow Right keyboard navigation on the track
 *   - Slide visibility tracking for aria-current on dots
 */

'use strict';

// ---------------------------------------------------------------------------
// Single slider component controller
// ---------------------------------------------------------------------------
class SliderController {
  /**
   * @param {HTMLElement} root - .mai-slider container
   */
  constructor(root) {
    this.root  = root;
    this.track = root.querySelector('.mai-slider__track');

    if (!this.track) return;

    this.slides = Array.from(this.track.children);
    this.index  = 0;

    this._buildControls();
    this._bindEvents();
    this._updateState();
  }

  // ---------------------------------------------------------------------------
  // Build DOM
  // ---------------------------------------------------------------------------

  _buildControls() {
    // Navigation buttons
    this.btnPrev = this._makeButton(
      'Previous slide',
      '\u2039',
      'mai-slider__btn mai-slider__btn--prev'
    );
    this.btnNext = this._makeButton(
      'Next slide',
      '\u203A',
      'mai-slider__btn mai-slider__btn--next'
    );

    // Dots container
    this.dotsContainer = document.createElement('div');
    this.dotsContainer.className = 'mai-slider__dots';

    this.dots = this.slides.map((_, i) => {
      const dot = document.createElement('button');
      dot.type  = 'button';
      dot.className = 'mai-slider__dot';
      dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
      dot.addEventListener('click', () => this._goToSlide(i));
      return dot;
    });

    this.dots.forEach(d => this.dotsContainer.appendChild(d));

    // Controls wrapper
    const controls = document.createElement('div');
    controls.className = 'mai-slider__controls';
    controls.appendChild(this.btnPrev);
    controls.appendChild(this.btnNext);

    this.root.appendChild(controls);
    this.root.appendChild(this.dotsContainer);
  }

  _makeButton(label, html, className) {
    const btn = document.createElement('button');
    btn.type  = 'button';
    btn.className = className;
    btn.setAttribute('aria-label', label);
    btn.innerHTML = html;
    return btn;
  }

  // ---------------------------------------------------------------------------
  // Events
  // ---------------------------------------------------------------------------

  _bindEvents() {
    this.btnPrev.addEventListener('click', () => this._prev());
    this.btnNext.addEventListener('click', () => this._next());

    this.track.addEventListener('keydown', (e) => {
      let handled = false;

      switch (e.key) {
        case 'ArrowLeft':
        case 'Left':
          this._prev();
          handled = true;
          break;

        case 'ArrowRight':
        case 'Right':
          this._next();
          handled = true;
          break;

        default:
          break;
      }

      if (handled) {
        e.preventDefault();
      }
    });

    // Track scroll position for dot indicator sync
    this.track.addEventListener('scroll', () => this._syncDotsFromScroll(), { passive: true });
  }

  // ---------------------------------------------------------------------------
  // Navigation
  // ---------------------------------------------------------------------------

  _prev() {
    if (this.index > 0) {
      this._goToSlide(this.index - 1);
    }
  }

  _next() {
    if (this.index < this.slides.length - 1) {
      this._goToSlide(this.index + 1);
    }
  }

  _goToSlide(index) {
    if (index < 0 || index >= this.slides.length) return;

    this.index = index;
    const slide = this.slides[index];
    slide.scrollIntoView({
      behavior: 'smooth',
      block:    'nearest',
      inline:   'start'
    });

    // Set focus to the track for continued keyboard navigation
    this.track.focus({ preventScroll: true });

    this._updateState();
  }

  // ---------------------------------------------------------------------------
  // State sync
  // ---------------------------------------------------------------------------

  _syncDotsFromScroll() {
    const center = this.track.scrollLeft + (this.track.offsetWidth / 2);

    let closestIndex = 0;
    let closestDist  = Infinity;

    this.slides.forEach((slide, i) => {
      const slideCenter = slide.offsetLeft + (slide.offsetWidth / 2);
      const dist = Math.abs(center - slideCenter);

      if (dist < closestDist) {
        closestDist  = dist;
        closestIndex = i;
      }
    });

    if (closestIndex !== this.index) {
      this.index = closestIndex;
      this._updateState();
    }
  }

  _updateState() {
    // Update dots
    this.dots.forEach((dot, i) => {
      if (i === this.index) {
        dot.setAttribute('aria-current', 'true');
        dot.classList.add('is-active');
      } else {
        dot.removeAttribute('aria-current');
        dot.classList.remove('is-active');
      }
    });

    // Update button states
    this.btnPrev.disabled = (this.index === 0);
    this.btnNext.disabled = (this.index === this.slides.length - 1);
  }
}

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.mai-slider').forEach(root => {
    new SliderController(root);
  });
});
