# Image Size Limit

Image Size Limit adds an image-specific upload limit under **Settings → Media**. It is a maintained continuation of the discontinued [WP Image Size Limit plugin](https://wordpress.org/plugins/wp-image-size-limit/).

The setting lets administrators prevent unnecessarily large images while preserving WordPress's server limit for other file types such as audio, video, and documents.

## Requirements

- WordPress 5.8 or newer
- PHP 7.4 or newer

## Installation

Install the plugin from the WordPress plugin directory, or upload a release ZIP on the Plugins screen. After activation, open **Settings → Media** and enter the maximum image size in KB.

Existing values stored by earlier releases remain compatible.

## Development

Install the development dependencies and run all checks:

```bash
composer install
composer check
```

The check command runs WordPress Coding Standards, PHPStan, and PHPUnit.

## Changelog

### 1.1.0

- Prevent duplicate hook registration.
- Detect images with WordPress file type checks instead of trusting the browser MIME type.
- Preserve exact server limits and consistently format file sizes.
- Replace global uploader CSS with a scoped media-screen notice.
- Improve validation, escaping, translations, and plugin metadata.
- Add automated tests, coding standards, static analysis, and a PHP version CI matrix.

### 1.0.5

- Fixed coding standards.
- Made the plugin translatable.

### 1.0.4

- Latest release from Sean Butze.
