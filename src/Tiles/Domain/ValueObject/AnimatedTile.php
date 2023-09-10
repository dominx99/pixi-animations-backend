<?php

declare(strict_types=1);

namespace App\Tiles\Domain\ValueObject;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final readonly class AnimatedTile
{
    /** @param Collection<Tile> $tiles */
    public function __construct(
        public Position $position,
        public ArrayCollection $tiles,
    ) {
    }
}
