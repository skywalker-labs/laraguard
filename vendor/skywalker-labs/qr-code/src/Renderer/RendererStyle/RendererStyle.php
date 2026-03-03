<?php

declare(strict_types=1);

namespace Skywalker\QrCode\Renderer\RendererStyle;

use Skywalker\QrCode\Renderer\Eye\EyeInterface;
use Skywalker\QrCode\Renderer\Eye\ModuleEye;
use Skywalker\QrCode\Renderer\Module\ModuleInterface;
use Skywalker\QrCode\Renderer\Module\SquareModule;

final class RendererStyle
{
    private $module;

    /**
     * @var EyeInterface|null
     */
    private $eye;

    private $fill;

    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $margin;

    public function __construct(
        int $size,
        int $margin = 4,
        ModuleInterface $module = null,
        EyeInterface $eye = null,
        Fill $fill = null
    ) {
        $this->size = $size;
        $this->margin = $margin;
        $this->module = $module ?: SquareModule::instance();
        $this->eye = $eye ?: new ModuleEye($this->module);
        $this->fill = $fill ?: Fill::default();
    }

    public function withSize(int $size): self
    {
        $style = clone $this;
        $style->size = $size;
        return $style;
    }

    public function withMargin(int $margin): self
    {
        $style = clone $this;
        $style->margin = $margin;
        return $style;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getMargin(): int
    {
        return $this->margin;
    }

    public function getModule(): ModuleInterface
    {
        return $this->module;
    }

    public function getEye(): EyeInterface
    {
        return $this->eye;
    }

    public function getFill(): Fill
    {
        return $this->fill;
    }
}

