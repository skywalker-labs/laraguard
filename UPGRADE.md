# Upgrading Laraguard

## Upgrade to 1.0.0 (Skywalker Toolkit Edition)

Laraguard 1.0.0 is the first stable release in the **Skywalker Toolkit** ecosystem. This version introduces full support for Laravel 10 through 12 and improved security architecture.

### 1. Dependency Update
Ensure your `composer.json` includes the correct requirements for the 1.0 release:

```json
"require": {
    "skywalker-labs/toolkit": "^1.0|^2.0",
    "bacon/bacon-qr-code": "^3.0"
}
```

Install the required DBAL package for schema upgrades:
```bash
composer require --dev doctrine/dbal
```

### 2. Service Provider Initialization
The `LaraguardServiceProvider` now extends `Skywalker\Support\Providers\PackageServiceProvider`. The service provider handles resource loading automatically. Ensure any previous manual view/translation registrations are removed to avoid duplication.

### 3. Model Factories
The `TwoFactorAuthentication` model factory is now under the `Database\Factories\Skywalker\Laraguard\Eloquent` namespace. Update your `composer.json` autoload-dev if you use factories in your tests:

```json
"autoload-dev": {
    "psr-4": {
        "Database\\Factories\\Skywalker\\Laraguard\\": "database/factories"
    }
}
```

### 4. Encryption Shield
Laraguard 1.0.0 enforces encryption for Shared Secrets and Recovery Codes (RFC 6238).

If coming from an unencrypted legacy version:
1. Publish the upgrade migration:
   `php artisan vendor:publish --provider="Skywalker\Laraguard\LaraguardServiceProvider" --tag="upgrade"`
2. Run migrations:
   `php artisan migrate`

The migration will handle background encryption of existing secrets.

