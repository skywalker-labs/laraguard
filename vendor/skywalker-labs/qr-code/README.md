# Skywalker QR Code generator

[![PHP CI](https://github.com/skywalker-labs/qr-code/actions/workflows/ci.yml/badge.svg)](https://github.com/skywalker-labs/qr-code/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/skywalker-labs/qr-code/v/stable)](https://packagist.org/packages/skywalker-labs/qr-code)
[![Total Downloads](https://poser.pugx.org/skywalker-labs/qr-code/downloads)](https://packagist.org/packages/skywalker-labs/qr-code)
[![License](https://poser.pugx.org/skywalker-labs/qr-code/license)](https://packagist.org/packages/skywalker-labs/qr-code)

## Introduction

Skywalker QR Code represents a high-performance and highly customizable QR code generator for PHP, maintained by Skywalker Labs. It is a port of the QR code portion of the ZXing library, featuring an optimized Reed Solomon codec implementation for PHP.

## Example usage

```php
use Skywalker\QrCode\Renderer\ImageRenderer;
use Skywalker\QrCode\Renderer\Image\ImagickImageBackEnd;
use Skywalker\QrCode\Renderer\RendererStyle\RendererStyle;
use Skywalker\QrCode\Writer;

$renderer = new ImageRenderer(
    new RendererStyle(400),
    new ImagickImageBackEnd()
);
$writer = new Writer($renderer);
$writer->writeFile('Hello World!', 'qrcode.png');
```

## Available image renderer back ends

Skywalker QrCode comes with multiple back ends for rendering images. Currently included are the following:

- `ImagickImageBackEnd`: renders raster images using the Imagick library
- `SvgImageBackEnd`: renders SVG files using XMLWriter
- `EpsImageBackEnd`: renders EPS files

### GDLib Renderer

GD library has so many limitations, that GD support is not added as backend, but as separated renderer.
Use `GDLibRenderer` instead of `ImageRenderer`. These are the limitations:

- Does not support gradient.
- Does not support any curves, so you QR code is always squared.

Example usage:

```php
use Skywalker\QrCode\Renderer\GDLibRenderer;
use Skywalker\QrCode\Writer;

$renderer = new GDLibRenderer(400);
$writer = new Writer($renderer);
$writer->writeFile('Hello World!', 'qrcode.png');
```

## Development

To run unit tests, you need to have [Node.js](https://nodejs.org/en) and the pixelmatch library installed. Running
`npm install` will install this for you.
