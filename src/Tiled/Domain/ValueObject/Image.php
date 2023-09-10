<?php

declare(strict_types=1);

namespace App\Tiled\Domain\ValueObject;

use JMS\Serializer\Annotation\XmlAttribute;

final class Image
{
    public function __construct(
        #[XmlAttribute]
        public string $source,
        #[XmlAttribute]
        public int $width,
        #[XmlAttribute]
        public int $height,
    ) {
    }
}
