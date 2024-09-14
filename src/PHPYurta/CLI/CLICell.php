<?php

declare(strict_types=1);

namespace PHPYurta\CLI;

class CLICell
{
    public function __construct(
        protected string $text,
        protected int $width = 0,
        protected int $align = STR_PAD_RIGHT
    ) {
    }

    public function __toString(): string
    {
        return str_pad($this->text, $this->width, ' ', $this->align);
    }

    public function setWidth(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function setAlign(int $align): static
    {
        $this->align = $align;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
