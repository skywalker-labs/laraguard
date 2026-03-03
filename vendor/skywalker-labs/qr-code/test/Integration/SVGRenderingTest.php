<?php

declare(strict_types=1);

namespace Skywalker\QrCodeTest\Integration;

use Skywalker\QrCode\Renderer\Color\Rgb;
use Skywalker\QrCode\Renderer\Image\SvgImageBackEnd;
use Skywalker\QrCode\Renderer\ImageRenderer;
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
final class SVGRenderingTest extends TestCase
{
    use MatchesSnapshots;

    public function testGenericQrCode(): void
    {
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString('Hello World!');

        $this->assertMatchesXmlSnapshot($svg);
    }

    public function testQrWithGradientGeneratesDifferentIdsForDifferentGradients()
    {
        $types = ['HORIZONTAL', 'VERTICAL'];

        foreach ($types as $type) {
            $gradient = new Gradient(
                new Rgb(0, 0, 0),
                new Rgb(255, 0, 0),
                GradientType::$type()
            );
            $renderer = new ImageRenderer(
                new RendererStyle(
                    400,
                    4,
                    null,
                    null,
                    Fill::withForegroundGradient(
                        new Rgb(255, 255, 255),
                        $gradient,
                        EyeFill::inherit(),
                        EyeFill::inherit(),
                        EyeFill::inherit()
                    )
                ),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $svg = $writer->writeString('Hello World!');

            $this->assertMatchesXmlSnapshot($svg);
        }
    }
}
