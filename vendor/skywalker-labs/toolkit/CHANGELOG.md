# Changelog

All notable changes to `laravel-toolkit` will be documented in this file.

## [1.0.0] - 2026-02-06

### Added

- Initial release of the rebranded **Laravel Toolkit**.
- API Utilities (`ApiResponse` trait).
- Data Transfer Objects (DTOs) base class.
- Enhanced Validation Rules (`StrongPassword`, `Slug`, `HexColor`, `Base64`).
- Collection & String Macros (`toCamelCase`, `toKebabCase`, `isBase64`).
- Enum Utility Helper.
- Custom Blade Directives (`@active`, `@money`, `@date`).
- Infrastructure Tools (`HasContext` logging, `InteractsWithIO` console).

### Changed

- Removed `declare(strict_types=1);` from all files for broader compatibility.
- Renamed package from `skywalker/support` to `ermradulsharma/laravel-toolkit`.
- Completely rewritten and modernized documentation.
