<?php

declare(strict_types=1);

namespace App\Tiled\Domain\ValueObject;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlList;

final class Animation
{
    /** @var Collection<Frame> */
    #[Type("Doctrine\Common\Collections\Collection<App\Tiled\Domain\ValueObject\Frame>")]
    #[XmlList(inline: true, entry: "frame")]
    private Collection $frames;

    public function __construct(
    ) {
        $this->frames = new ArrayCollection([]);
    }

    public function addFrame(Frame $frame): void
    {
        $this->frames->add($frame);
    }

    public function getFrames(): Collection
    {
        return $this->frames;
    }
}
