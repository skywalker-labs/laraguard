<?php

declare(strict_types=1);

namespace Skywalker\QrCode;

use Skywalker\QrCode\Common\ErrorCorrectionLevel;
use Skywalker\QrCode\Common\Version;
use Skywalker\QrCode\Encoder\Encoder;
use Skywalker\QrCode\Exception\InvalidArgumentException;
use Skywalker\QrCode\Renderer\RendererInterface;

/**
 * QR code writer.
 */
final class Writer
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * Creates a new writer with a specific renderer.
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Writes QR code and returns it as string.
     *
     * Content is a string which *should* be encoded in UTF-8, in case there are
     * non ASCII-characters present.
     *
     * @throws InvalidArgumentException if the content is empty
     */
    public function writeString(
        string $content,
        string $encoding = Encoder::DEFAULT_BYTE_MODE_ENCODING,
        ?ErrorCorrectionLevel $ecLevel = null,
        ?Version $forcedVersion = null
    ): string {
        if (strlen($content) === 0) {
            throw new InvalidArgumentException('Found empty contents');
        }

        if (null === $ecLevel) {
            $ecLevel = ErrorCorrectionLevel::L();
        }

        return $this->renderer->render(Encoder::encode($content, $ecLevel, $encoding, $forcedVersion));
    }

    /**
     * Writes QR code to a file.
     *
     * @see Writer::writeString()
     */
    public function writeFile(
        string $content,
        string $filename,
        string $encoding = Encoder::DEFAULT_BYTE_MODE_ENCODING,
        ?ErrorCorrectionLevel $ecLevel = null,
        ?Version $forcedVersion = null
    ): void {
        file_put_contents($filename, $this->writeString($content, $encoding, $ecLevel, $forcedVersion));
    }
}

