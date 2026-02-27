# TYPO3 Extension: theme

This extension provides a way to load assets (stylesheets and JavaScripts) and backend theme configurations from other active packages in a TYPO3 instance.

## Features

- **Automated Asset Inclusion**: Automatically register stylesheets and JavaScripts for frontend and backend.
- **Backend Theme Management**: Easily override TYPO3 backend settings like logos, favicons, and login images from configuration files.
- **Configuration Merging**: Merges configuration files from all active packages, allowing for modular theme development.

## Installation

```bash
composer require maispace/theme
```

## Usage

Create configuration files in your extension's `Configuration/` directory:

### StyleSheets.php
```php
<?php
return [
    'frontend' => [
        'my_stylesheet' => [
            'source' => 'EXT:my_extension/Resources/Public/Css/style.css',
            'site-identifier' => 'my_site' // Optional: filter by site identifier
        ],
    ],
];
```

### JavaScripts.php
```php
<?php
return [
    'frontend' => [
        'my_script' => [
            'source' => 'EXT:my_extension/Resources/Public/Js/script.js',
            'site-identifier' => 'my_site'
        ],
    ],
];
```

### BackendTheme.php
```php
<?php
return [
    'backendLogo' => 'EXT:my_extension/Resources/Public/Icons/logo.svg',
    'loginBackgroundImage' => 'EXT:my_extension/Resources/Public/Images/login-bg.jpg',
];
```

The `theme` extension will automatically find these files in any active package and apply the configurations.

## Development

-   **Backend Theme**: Call `GeneralUtility::makeInstance(BackendTheme::class)->registerBackendTheme()` in your `ext_localconf.php` to apply backend settings.

## License

GPL-2.0-or-later

