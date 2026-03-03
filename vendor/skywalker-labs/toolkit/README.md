<div align="center">

# 🧱 Skywalker Support: High-Octane Foundation
### *The Architectural Core for Modern Laravel Microservices*

[![Latest Version](https://img.shields.io/badge/version-3.4.0-darkgray.svg?style=for-the-badge)](https://packagist.org/packages/skywalker-labs/support)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg?style=for-the-badge)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.4+-777bb4.svg?style=for-the-badge)](https://php.net)
[![Pattern](https://img.shields.io/badge/Pattern-Clean_Architecture-blue.svg?style=for-the-badge)](https://github.com/skywalker-labs/support)

---

**Skywalker Support** is a curated collection of elite traits, service providers, and classes that form the backbone of all Skywalker-Labs packages. It enforces strict **API Standardization**, **Component Discovery**, and **Enterprise-Grade Validation**.

</div>

## 💎 The Foundation Advantage

Why use our support layer?
- 🔄 **Standardized API Responses:** Ensure every JSON response across your suite follows the same schema.
- 📡 **Blueprint Discovery:** Automated discovery of routes, commands, and providers.
- 🛡️ **Advanced Stub Engine:** Programmatically generate boiler-plate with complex variable injections.

---

## 🔥 Shared Core Features

### 1. Unified API Protocol
Enforce consistency across your entire microservice fleet:
```php
use Skywalker\Support\Http\Concerns\ApiResponse;

class Controller {
    use ApiResponse;

    public function index() {
        return $this->apiSuccess(['users' => []], "Fetched successfully");
    }
}
```

### 2. High-Performance Discovery
Automatically registers package components without manual `composer.json` overhead or config bloat.

### 3. Logic-Rich Traits
From `Enum` utilities to `Database` scoping, these traits are optimized for PHP 8.4+ and high-concurrency environments.

---

## ⚡ Efficiency Benchmarks

| Activity | Manual Laravel | Skywalker Support | Efficiency |
| :--- | :--- | :--- | :--- |
| **New Package Setup** | 30 mins | **2 mins** | 15x Faster |
| **API Debugging** | High (Varied schema) | **Zero (Standardized)** | Elite DX |
| **Memory Overlapping** | High | **Low (Lazy Discovery)** | Optimized |

---

## 🛠️ Elite Usage (PHP 8.4+)

### Standard Response Flow
```php
protected array $response {
    get => $this->apiSuccess($data);
}
```

### Component Registration
```php
// In your Service Provider
public function register(): void
{
    $this->discoverAll(__DIR__ . '/../src');
}
```

---

## 🗺️ Roadmap
- [x] **v3.4**: Laravel 12 Discovery Engine.
- [ ] **v3.5**: Shared Value-Object Library.
- [ ] **v4.0**: Distributed Event Bus Support.

---

Created & Maintained by **Skywalker-Labs**. The foundation for excellence.
