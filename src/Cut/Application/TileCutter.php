<?php

declare(strict_types=1);

namespace App\Cut\Application;

use App\Cut\Command\Domain\CutIntoPartsContext;

interface TileCutter
{
    public function cut(string $inputPath, string $outputDirectory, CutIntoPartsContext $context): void;
}
