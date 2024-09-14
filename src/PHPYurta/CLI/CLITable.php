<?php

declare(strict_types=1);

namespace PHPYurta\CLI;

class CLITable
{
    protected ?string $caption = null;
    protected CLIRows $header;
    protected CLIRows $body;
    protected CLIRows $footer; //TODO
    protected array $cellWidth = [];
    protected int $padding = 1;

    protected array $border = [
        'top-left' => '┌',
        'top-center' => '┬',
        'top-right' => '┐',

        'bottom-left' => '└',
        'bottom-center' => '┴',
        'bottom-right' => '┘',

        'middle-left' => '├',
        'middle-right' => '┤',

        'middle-center' => '┼',
        'horizontal' => '─',
        'vertical' => '│',
    ];
    protected bool $innerBorders = false;

    public function setCaption(string $caption): static
    {
        $this->caption = $caption;

        return $this;
    }

    public function addHeader(string $text): static
    {
        $this->header ??= new CLIRows();
        $this->header->getLastRow()->addCell($text);

        return $this;
    }

    public function setHeaders(array $textHeaders): static
    {
        $this->header ??= new CLIRows();
        $lastRow = $this->header->getLastRow();
        for ($i = 0; $i < count($textHeaders); $i++) {
            $lastRow->addCell($textHeaders[$i]);
        }

        return $this;
    }

    public function addCell(string $text): static
    {
        $this->body ??= new CLIRows();
        $this->body->getLastRow()->addCell($text);

        return $this;
    }

    public function addRow(?array $textCells = null): static
    {
        $this->body ??= new CLIRows();
        $this->body->addRow(new CLIRow());
        if ($textCells !== null) {
            $lastRow = $this->body->getLastRow();
            for ($i = 0; $i < count($textCells); $i++) {
                $lastRow->addCell((string) $textCells[$i]);
            }
        }

        return $this;
    }
    public function addBorderLine(): static
    {
        $this->body ??= new CLIRows();
        $this->body->addRow(new CLIRow());

        return $this;
    }

    public function getTable(): string
    {
        $columns = $this->calculateColumnWidth();

        $borderLine = $this->getHorizontalBorder($columns) . PHP_EOL;

        $sectors = array_filter([
            $this->header ?? null,
            $this->body ?? null,
            $this->footer ?? null,
        ]);

        $output = [
            $this->getHorizontalBorder(
                $columns,
                $this->border['top-left'],
                $this->border['top-center'],
                $this->border['top-right'],
            ) . PHP_EOL
        ];
        foreach ($sectors as $sector)
        {
            $rows = [];
            foreach ($sector as $row) {
                $cells = [];
                for ($i = 0; $i < count($columns); $i++) {
                    $cells[] = $this->getCellOutput(
                        $columns[$i],
                        $row[$i] ?? null,
                    );
                }

                $rows[] = $this->border['vertical'] . implode(
                    $this->border['vertical'],
                    $cells
                ) . $this->border['vertical'] . PHP_EOL;
            }

            $output[] = (count($output) > 1 ? $borderLine : '') . implode(
                $this->innerBorders ? $borderLine : '',
                $rows
            );
        }
        $output[] = $this->getHorizontalBorder(
            $columns,
            $this->border['bottom-left'],
            $this->border['bottom-center'],
            $this->border['bottom-right'],
        ) . PHP_EOL;

        if ($this->caption !== null) {
            array_unshift($output, str_pad(
                $this->caption, mb_strlen($borderLine), ' ', STR_PAD_BOTH
            ) . PHP_EOL);
        }
        $output = implode($output);
        if (PHP_SAPI !== 'cli') {
            $output = '<pre>' . $output . '</pre>';
        }

        return $output;
    }

    public function printOut(): void
    {
        echo $this->getTable();
    }

    private function getCellOutput(int $width, ?CLICell $cell = null)
    {
        $padding = str_repeat(' ', $this->padding);
        $text = $cell ? $cell->getText() : '';
        $text = preg_replace([
            '/\s+/u',
            '/\n\t/u',
            '/\x1b[[][^A-Za-z]*[A-Za-z]/u',
        ], [' ', ''], $text);

        return $padding . str_pad($text, $width, ' ', STR_PAD_RIGHT) . $padding;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if ($name === 'header' || $name === 'body' || $name === 'footer') {
            $this->{$name} = new CLIRows();

            return $this->{$name};
        }

        throw new \Exception("Property $name not found");
    }

    private function calculateColumnWidth(?CLIRows $sector = null): array
    {
        if ($sector === null) {
            $sectors = [
                isset($this->header) ? $this->calculateColumnWidth($this->header) : null,
                isset($this->body) ? $this->calculateColumnWidth($this->body) : null,
                isset($this->footer) ? $this->calculateColumnWidth($this->footer) : null,
            ];
            $this->cellWidth = [];
            foreach ($sectors as $sector) {
                if ($sector !== null) {
                    for ($i = 0; $i < count($sector); $i++) {
                        $current = $this->cellWidth[$i] ?? 0;
                        $this->cellWidth[$i] = max($current, $sector[$i]);
                    }
                }
            }

            return $this->cellWidth;
        }

        /**
         * @var CLIRows $sector
         * @var CLIRow $row
         */
        $columns = [];
        foreach ($sector as $row) {
            for ($i = 0; $i < $row->count(); $i++) {
                $current = $columns[$i] ?? 0;
                $columns[$i] = max($current, mb_strlen($row[$i]->getText()));
            }
        }

        return $columns;
    }

    private function getHorizontalBorder(
        array $columns,
        ?string $left = null,
        ?string $center = null,
        ?string $right = null,
    ): string
    {
        $left ??= $this->border['middle-left'];
        $center ??= $this->border['middle-center'];
        $right ??= $this->border['middle-right'];

        $output = array_map(function ($width) {
            return str_repeat($this->border['horizontal'], $width + $this->padding * 2);
        }, $columns);
        $output = $left . implode($center, $output) . $right;

        return $output;
    }
}
