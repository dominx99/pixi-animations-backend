<?php

declare(strict_types=1);

namespace App\Tiled\Domain\ValueObject;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot('tileset')]
final class Tileset
{
    public Image $image;
    /** @var Collection<Tile> */
    #[Type("Doctrine\Common\Collections\Collection<App\Tiled\Domain\ValueObject\Tile>")]
    #[XmlList(inline: true, entry: "tile")]
    private Collection $tiles;

    private function __construct(
        Image $image,
        #[XmlAttribute]
        public string $version,
        #[XmlAttribute]
        #[SerializedName('tiledversion')]
        public string $tiledVersion,
        #[XmlAttribute]
        public string $name,
        #[XmlAttribute]
        #[SerializedName('tilewidth')]
        public int $tileWidth,
        #[XmlAttribute]
        #[SerializedName('tileheight')]
        public int $tileHeight,
        #[XmlAttribute]
        #[SerializedName('tilecount')]
        public int $tileCount,
        #[XmlAttribute]
        public int $columns,
    ) {
        $this->image = $image;
        $this->tiles = new ArrayCollection();
    }

    public static function new(
        Image $image,
        string $name,
        int $tileWidth,
        int $tileHeight,
        int $tileCount,
        int $columns,
    ): Tileset {
        return new self(
            $image,
            '1.10',
            '1.10.2',
            $name,
            $tileWidth,
            $tileHeight,
            $tileCount,
            $columns,
        );
    }

    public function addTile(Tile $tile): void
    {
        $this->tiles->add($tile);
    }

    public function getTiles(): array
    {
        return $this->tiles->toArray();
    }
}
