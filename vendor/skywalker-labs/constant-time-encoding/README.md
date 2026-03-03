# Skywalker Constant-Time Encoding

[![Build Status](https://github.com/skywalker-labs/constant-time-encoding/actions/workflows/ci.yml/badge.svg)](https://github.com/skywalker-labs/constant-time-encoding/actions)
[![Static Analysis](https://github.com/skywalker-labs/constant-time-encoding/actions/workflows/psalm.yml/badge.svg)](https://github.com/skywalker-labs/constant-time-encoding/actions)
[![Latest Stable Version](https://poser.pugx.org/skywalker-labs/constant-time-encoding/v/stable)](https://packagist.org/packages/skywalker-labs/constant-time-encoding)
[![Latest Unstable Version](https://poser.pugx.org/skywalker-labs/constant-time-encoding/v/unstable)](https://packagist.org/packages/skywalker-labs/constant-time-encoding)
[![License](https://poser.pugx.org/skywalker-labs/constant-time-encoding/license)](https://packagist.org/packages/skywalker-labs/constant-time-encoding)
[![Downloads](https://img.shields.io/packagist/dt/skywalker-labs/constant-time-encoding.svg)](https://packagist.org/packages/skywalker-labs/constant-time-encoding)

Constant-time implementations of RFC 4648 encoding (Base64, Base32, Hex) and other variants.

This library aims to offer character encoding functions that do not leak information about what you are encoding/decoding via processor cache misses.

## Features

- **Constant-Time Encoding/Decoding**: Prevents cache-timing attacks.
- **Multiple Encodings**:
  - **Base64** (RFC 4648)
  - **Base64UrlSafe** (URL-safe variant)
  - **Base64DotSlash** (Crypt version, `./[A-Z][a-z][0-9]`)
  - **Base64DotSlashOrdered** (Ordered variant, `[.-9][A-Z][a-z]`)
  - **Base32** (RFC 4648)
  - **Base32Hex** (RFC 4648 base32hex)
  - **Hex** (Base16)
- **Security**: Uses `pack()` and `unpack()` instead of `chr()` and `ord()` for safer bit manipulation.
- **Robust**: Strict padding validation (optional).

## Requirements

- PHP 7.4 or newer (including PHP 8.x).

## Installation

```sh
composer require skywalker-labs/constant-time-encoding
```

## Usage

You can use the `Skywalker\ConstantTime\Encoding` class as a facade for all encoding methods, or use specific classes directly.

### Using the Encoding Facade

```php
use Skywalker\ConstantTime\Encoding;

// Sample data
$data = random_bytes(32);

// Base64
echo Encoding::base64Encode($data), "\n";

// Base32 (Uppercase)
echo Encoding::base32EncodeUpper($data), "\n";

// Base32 (Standard)
echo Encoding::base32Encode($data), "\n";

// Hex (Base16)
echo Encoding::hexEncode($data), "\n";
echo Encoding::hexEncodeUpper($data), "\n";
```

### Using Specific Classes

If you only need a specific encoding, you can import the relevant class directly:

```php
use Skywalker\ConstantTime\Base64;
use Skywalker\ConstantTime\Base32;
use Skywalker\ConstantTime\Hex;

$data = random_bytes(32);

// Encode
$b64 = Base64::encode($data);
$b32 = Base32::encode($data);
$hex = Hex::encode($data);

// Decode
$raw = Base64::decode($b64);
```

### Output Example

```text
1VilPkeVqirlPifk5scbzcTTbMT2clp+Zkyv9VFFasE=
2VMKKPSHSWVCVZJ6E7SONRY3ZXCNG3GE6ZZFU7TGJSX7KUKFNLAQ====
2vmkkpshswvcvzj6e7sonry3zxcng3ge6zzfu7tgjsx7kukfnlaq====
d558a53e4795aa2ae53e27e4e6c71bcdc4d36cc4f6725a7e664caff551456ac1
D558A53E4795AA2AE53E27E4E6C71BDCC4D36CC4F6725A7E664CAFF551456AC1
```

## Security

This library was designed to eliminate cache-timing side-channels from encoding operations. It is intended for use in cryptographic contexts where timing leaks could expose sensitive data.

## Support

If your company uses this library in their products or services, you may be interested in [purchasing a support contract from Skywalker Labs](https://skywalker-labs.com/).

## License

This project is licensed under the MIT License - see the [LICENSE.txt](LICENSE.txt) file for details.


