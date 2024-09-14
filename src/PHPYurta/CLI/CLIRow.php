<?php

declare(strict_types=1);

namespace PHPYurta\CLI;

class CLIRow extends \SplQueue
{
    public function addCell(string $text): static
    {
        $this[] = new CLICell($text);

        return $this;
    }
}
