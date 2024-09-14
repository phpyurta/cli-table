<?php

declare(strict_types=1);

namespace PHPYurta\CLI;

class CLIRows extends \SplQueue
{
    public function addRow(CLIRow $row): static
    {
        $this[] = $row;

        return $this;
    }

    public function getLastRow(): CLIRow
    {
        if ($this->isEmpty()) {
            $this->addRow(new CLIRow());
        }

        return $this->top();
    }
}
