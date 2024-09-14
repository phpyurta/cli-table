<?php

require_once __DIR__ . '/vendor/autoload.php';

use PHPYurta\CLI\CLITable;

$table = new CLITable();
$table
    ->setCaption('Bordered table with default settings')
    ->addHeader('~-(^._.^)')
    ->addHeader('Lifespan')
    ->addHeader('Origin')
    ->addHeader('Other names')
    ->addRow()
        ->addCell('Maine Coon')
        ->addCell('13 – 14 years')
        ->addCell('Maine, United States')
        ->addCell('Coon Cat, Maine Cat, Maine Shag')
    ->addRow()
        ->addCell('Persian cat')
        ->addCell('12 – 17 years')
        ->addCell('Iran, Afghanistan')
        ->addCell('Persian Longhair, Shirazi')
    ->addRow()
        ->addCell('Bengal cat')
        ->addCell('12 – 16 years')
        ->addCell('United States')
        ->addCell('I do not know :)')
    ->printOut()
;


