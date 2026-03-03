<?php
declare(strict_types = 1);

namespace Skywalker\QrCode\Renderer;

use Skywalker\QrCode\Encoder\QrCode;

interface RendererInterface
{
    public function render(QrCode $qrCode) : string;
}

