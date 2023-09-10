<?php

declare(strict_types=1);

namespace App\Tiled\Domain\ValueObject;

use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[XmlRoot('tile')]
final readonly class Tile
{
    public function __construct(
        #[XmlAttribute]
        public int $id,
        public Animation $animation
    )
    {
    }
}
