<div align="center">

# Laraguard Elite 🛡️

[![Latest Version on Packagist](https://img.shields.io/packagist/v/skywalker-labs/laraguard.svg?style=flat-square)](https://packagist.org/packages/skywalker-labs/laraguard)
[![Quality Score](https://img.shields.io/scrutinizer/g/skywalker-labs/laraguard.svg?style=flat-square)](https://scrutinizer-ci.com/g/skywalker-labs/laraguard)
[![Total Downloads](https://img.shields.io/packagist/dt/skywalker-labs/laraguard.svg?style=flat-square)](https://packagist.org/packages/skywalker-labs/laraguard)
[![Laravel 5.5 - 12+](https://img.shields.io/badge/Laravel-5.5--12%2B-red.svg)
[![PHP Version](https://img.shields.io/badge/PHP-8.1+-777bb4.svg?style=for-the-badge)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-blue.svg?style=for-the-badge)](LICENSE.md)

---

**Laraguard Elite** is the advanced 2FA security suite for Laravel (5.5 - 12+). It transforms standard TOTP authentication into a premium, audit-ready, and modern security experience.

## Elite Feature Set
- 🚀 **Artisan CLI**: Manage and monitor 2FA status via terminal.
- 📋 **Audit Logging**: Comprehensive activity logs for all 2FA events.
- **Passkeys (WebAuthn)**: Biometric and hardware key support.
- **Intelligent Armor**: Adaptive rate limiting and trusted devices.
- **Security Hub**: Real-time notifications for Slack and Discord.
- **Magic Links**: Emergency 2FA-less access via signed URLs.
- **Filament Support**: Native plugin for Filament PHP admin panels.
- 🎨 **Premium UI**: Beautiful Blade components for barcodes and status badges.
- 🔑 **Passkeys Ready**: Foundation for WebAuthn / Biometric authentication.
- 📦 **Ecosystem Sync**: Fully integrated with the `skywalker-labs` toolkit.
</div>

## 🛡️ Why Laraguard Elite?

While other packages expose your 2FA status in simple queries, Laraguard uses an **Architecture of Stealth**:
- 🕵️ **Hidden Relationships:** The `twoFactorAuth` data is automatically masked from `toArray()` and `toJson()` to prevent accidental PII leaks.
- 💻 **Safe Device Fingerprinting:** Allow users to "Remember this device" securely with expiring, IP-bound tokens.
- 🏗️ **Skywalker Toolkit Foundation:** Built on the robust [Skywalker Toolkit](https://github.com/skywalker-labs/toolkit) base package for better maintainability and performance.
- ⚡ **Zero-Config Implementation:** Simply add the trait and you're multi-factor ready.

---

## 🔥 Killer Features

### 1. Stealth Pivot Masking
Prevents PII leakage by hiding the 2FA relationship from the parent model globally. No more accidental API exposures of sensitive security metadata.

### 2. Automated Recovery Logic
Generates encrypted recovery codes out-of-the-box. Includes automated depletion events and built-in migration support for seamless upgrades.

### 3. TOTP Offset Resilience
Handles time-drift automatically using configurable windows, ensuring users aren't locked out due to minor server clock desync.

---

## ⚡ Comparison Table

| Feature | Standard 2FA | Laraguard Elite |
| :--- | :--- | :--- |
| **Foundation** | Standalone | **Skywalker Toolkit Base** |
| **Recovery Codes** | Manual Setup | **Automated & Encrypted** |
| **Device Memory** | Session Only | **IP-Bound Persistent Tokens** |
| **Privacy** | Visible DB Fields | **Stealth Relationship Masking** |
| **UX** | Intrusive | **Bypassable for Safe Devices** |

---

## 🛠️ Installation (PHP 8.1+)

### Installation

```bash
composer require skywalker-labs/laraguard
```

### Add Protection
```php
class User extends Authenticatable {
    use TwoFactorAuthentication;
}
```

### Manage 2FA
```php
// Confirm a code and enable 2FA
$user->confirmTwoFactorAuth($code);

// Generate new recovery codes
$user->generateRecoveryCodes();
```

### Advanced: Add Safe Device
```php
if ($user->isNotSafeDevice($request)) {
    // Challenge for 2FA
}

// After successful 2FA
$user->addSafeDevice($request);
```

---

## 🛡️ Enterprise Security & Privacy
- **Encrypted Storage:** Both Shared Secrets and Recovery Codes are encrypted using Laravel's native encrypter.
- **Cross-Database Support:** Fully compatible with SQLite, MySQL, and PostgreSQL (tested on Laravel 10 through 12).
- **Event-Driven Security:** `TwoFactorEnabled`, `TwoFactorDisabled`, and `TwoFactorRecoveryCodesDepleted` events for real-time monitoring.

---

Maintained by **[Skywalker-Labs](https://skywalker-labs.com/)**.
Lead Developer: **[Mradul Sharma](https://mradulsharma.vercel.app/)**
Original Author: **Italo Israel Baeza Cabrera**
