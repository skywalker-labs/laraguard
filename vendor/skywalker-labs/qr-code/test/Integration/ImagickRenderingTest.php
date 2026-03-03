<?php

declare(strict_types=1);

namespace Skywalker\QrCodeTest\Integration;

use Skywalker\QrCode\Renderer\Color\Rgb;
use Skywalker\QrCode\Renderer\Eye\SquareEye;
use Skywalker\QrCode\Renderer\Eye\PointyEye;
use Skywalker\QrCode\Renderer\Image\ImagickImageBackEnd;
use Skywalker\QrCode\Renderer\ImageRenderer;
use Skywalker\QrCode\Renderer\Module\SquareModule;
use Skywalker\QrCode\Renderer\RendererStyle\EyeFill;
use Skywalker\QrCode\Renderer\RendererStyle\Fill;
use Skywalker\QrCode\Renderer\RendererStyle\Gradient;
use Skywalker\QrCode\Renderer\RendererStyle\GradientType;
use Skywalker\QrCode\Renderer\RendererStyle\RendererStyle;
use Skywalker\QrCode\Writer;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

/**
 * @group integration
 */
final class ImagickRenderingTest extends TestCase
{
    use MatchesSnapshots;

    /**
     * @requires extension imagick
     */
    public function testGenericQrCode(): void
    {
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new ImagickImageBackEnd()
        );
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
     * @requires extension imagick
     */
    public function testIssue79(): void
    {
        $eye = SquareEye::instance();
        $squareModule = SquareModule::instance();

        $eyeFill = new EyeFill(new Rgb(100, 100, 55), new Rgb(100, 100, 255));
        $gradient = new Gradient(new Rgb(100, 100, 55), new Rgb(100, 100, 255), GradientType::HORIZONTAL());

        $renderer = new ImageRenderer(
            new RendererStyle(
                400,
                2,
                $squareModule,
                $eye,
                Fill::withForegroundGradient(new Rgb(255, 255, 255), $gradient, $eyeFill, $eyeFill, $eyeFill)
            ),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        $tempName = tempnam(sys_get_temp_dir(), 'test') . '.png';
        $writer->writeFile('https://apiroad.net/very-long-url', $tempName);

        if (!method_exists($this, 'assertMatchesImageSnapshot')) {
            $this->markTestSkipped('assertMatchesImageSnapshot is not available (requires spatie/pixelmatch-php)');
        }

        $this->assertMatchesImageSnapshot($tempName);
        unlink($tempName);
    }

    /**
     * @requires extension imagick
     */
    public function testIssue105(): void
    {
        $squareModule = SquareModule::instance();
        $pointyEye = PointyEye::instance();

        $renderer1 = new ImageRenderer(
            new RendererStyle(
                400,
                2,
                $squareModule,
                $pointyEye,
                Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(0, 0, 255))
            ),
            new ImagickImageBackEnd()
        );
        $writer1 = new Writer($renderer1);
        $tempName1 = tempnam(sys_get_temp_dir(), 'test') . '.png';
        $writer1->writeFile('rotation without eye color', $tempName1);

        if (!method_exists($this, 'assertMatchesImageSnapshot')) {
            $this->markTestSkipped('assertMatchesImageSnapshot is not available (requires spatie/pixelmatch-php)');
        }

        $this->assertMatchesImageSnapshot($tempName1);
        unlink($tempName1);

        $eyeFill = new EyeFill(new Rgb(255, 0, 0), new Rgb(0, 255, 0));

        $renderer2 = new ImageRenderer(
            new RendererStyle(
                400,
                2,
                $squareModule,
                $pointyEye,
                Fill::withForegroundColor(new Rgb(255, 255, 255), new Rgb(0, 0, 255), $eyeFill, $eyeFill, $eyeFill)
            ),
            new ImagickImageBackEnd()
        );
        $writer2 = new Writer($renderer2);
        $tempName2 = tempnam(sys_get_temp_dir(), 'test') . '.png';
        $writer2->writeFile('rotation with eye color', $tempName2);

        if (!method_exists($this, 'assertMatchesImageSnapshot')) {
            $this->markTestSkipped('assertMatchesImageSnapshot is not available (requires spatie/pixelmatch-php)');
        }

        $this->assertMatchesImageSnapshot($tempName2);
        unlink($tempName2);
    }
}
