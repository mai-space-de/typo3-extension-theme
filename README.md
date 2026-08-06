# maispace/mai-theme — TYPO3 Extension
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://www.php.net/)
[![TYPO3](https://img.shields.io/badge/TYPO3-13.4%20LTS-orange)](https://typo3.org/)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)

The **frontend component layer** for the entire extension set. All Fluid templates in feature extensions reference components defined here via native TYPO3 Fluid Components (Fluid 4.3+). `mai_theme` is a practical runtime requirement for every extension that renders frontend output.

**Requires:** TYPO3 13.4 LTS / 14.1 · PHP 8.2+

---

## Installation

```bash
composer require maispace/mai-theme
```

---

## Development

### Linting

```bash
composer lint:check     # Run all linters
composer lint:fix       # Fix auto-fixable issues
```

### Testing

```bash
composer test           # Run all tests
composer test:unit      # Run unit tests only
```

---

## License

GPL-2.0-or-later — see [LICENSE](../../LICENSE) for details.
