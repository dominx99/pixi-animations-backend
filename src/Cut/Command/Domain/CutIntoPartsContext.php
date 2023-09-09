<?php

declare(strict_types=1);

namespace App\Cut\Command\Domain;

final readonly class CutIntoPartsContext
{
    private function __construct(public int $tileWidth, public int $tileHeight) {
    }

    public static function new(int $tileWidth, int $tileHeight): self
    {
        return new self($tileWidth, $tileHeight);
    }

    /** @param array<string, int|string> $arguments */
    public static function fromArray(array $arguments): self
    {
        return new self(
            (int) $arguments['tileWidth'],
            (int) $arguments['tileHeight'],
        );
    }
}
