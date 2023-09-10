<?php

declare(strict_types=1);

namespace App\Tiled\Domain\ValueObject;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot('frame')]
final class Frame
{
    public function __construct(
        #[XmlAttribute]
        #[SerializedName('tileid')]
        private int $tileId,
        #[XmlAttribute]
        private int $duration
    ) {
    }

    public function getTileId(): int
    {
        return $this->tileId;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }
}
