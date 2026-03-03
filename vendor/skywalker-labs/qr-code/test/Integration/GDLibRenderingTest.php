<?php

declare(strict_types=1);

namespace Skywalker\QrCodeTest\Integration;

use Skywalker\QrCode\Exception\InvalidArgumentException;
use Skywalker\QrCode\Renderer\Color\Alpha;
use Skywalker\QrCode\Renderer\Color\Rgb;
use Skywalker\QrCode\Renderer\GDLibRenderer;
use Skywalker\QrCode\Renderer\Image\GDImageBackEnd;
use Skywalker\QrCode\Renderer\RendererStyle\EyeFill;
use Skywalker\QrCode\Renderer\RendererStyle\Fill;
use Skywalker\QrCode\Renderer\RendererStyle\Gradient;
use Skywalker\QrCode\Renderer\RendererStyle\GradientType;
use Skywalker\QrCode\Writer;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

/**
 * @group integration
 */
final class GDLibRenderingTest extends TestCase
{
    use MatchesSnapshots;

    /**
     * @requires extension gd
     */
    public function testGenericQrCode(): void
    {
        $renderer = new GDLibRenderer(400);
        $writer = new Writer($renderer);
        $tempName = tempnam(sys_get_temp_dir(), 'test') . '.png';
        $writer->writeFile('Hello World!', $tempName);

        if (!method_exists($this, 'assertMatchesImageSnapshot')) {
            $this->markTestSkipped('assertMatchesImageSnapshot is not available (requires spatie/pixelmatch-php)');
        }

        $this->assertMatchesImageSnapshot($tempName);
        unlink($tempName);
    }

    /**
     * @requires extension gd
     */
    public function testDifferentColorsQrCode(): void
    {
        $renderer = new GDLibRenderer(
            400,
            10,
            'png',
            9,
            Fill::withForegroundColor(
                new Alpha(25, new Rgb(0, 0, 0)),
                new Rgb(0, 0, 0),
                new EyeFill(new Rgb(220, 50, 50), new Alpha(50, new Rgb(220, 50, 50))),
                new EyeFill(new Rgb(50, 220, 50), new Alpha(50, new Rgb(50, 220, 50))),
                new EyeFill(new Rgb(50, 50, 220), new Alpha(50, new Rgb(50, 50, 220))),
            )
        );
        $writer = new Writer($renderer);
        $tempName = tempnam(sys_get_temp_dir(), 'test') . '.png';
        $writer->writeFile('Hello World!', $tempName);


        if (!method_exists($this, 'assertMatchesImageSnapshot')) {
            $this->markTestSkipped('assertMatchesImageSnapshot is not available');
        }
        $this->assertMatchesImageSnapshot($tempName);
        unlink($tempName);
    }


    /**
     * @requires extension gd
     */
    public function testFailsOnGradient(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('GDLibRenderer does not support gradients');

        new GDLibRenderer(
            400,
            10,
            'png',
            9,
            Fill::withForegroundGradient(
                new Alpha(25, new Rgb(0, 0, 0)),
                new Gradient(new Rgb(255, 255, 0), new Rgb(255, 0, 255), GradientType::DIAGONAL()),
                new EyeFill(new Rgb(220, 50, 50), new Alpha(50, new Rgb(220, 50, 50))),
                new EyeFill(new Rgb(50, 220, 50), new Alpha(50, new Rgb(50, 220, 50))),
                new EyeFill(new Rgb(50, 50, 220), new Alpha(50, new Rgb(50, 50, 220))),
            )
        );
    }

    /**
     * @requires extension gd
     */
    public function testFailsOnInvalidFormat(): void
    {
        $renderer = new GDLibRenderer(400, 4, 'tiff');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Supported image formats are jpeg, png and gif, got: tiff');

        $writer = new Writer($renderer);
        $tempName = tempnam(sys_get_temp_dir(), 'test') . '.png';
        $writer->writeFile('Hello World!', $tempName);
    }
}
