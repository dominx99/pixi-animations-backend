<?php

declare(strict_types=1);

namespace App\Merge\UI\Http;

use App\Tiles\Application\OrderConfig;
use App\Tiles\Application\TilesOrderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class MergePixiAnimationTileset extends AbstractController
{
    public function __construct(private readonly TilesOrderer $tilesOrderer)
    {
    }

    #[Route('/api/merge/pixi-animation-tileset', name: 'merge_pixi_animation_tileset', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        $tiles = $this->tilesOrderer->order($content['tileset']['tiles'], new OrderConfig(
            (int) $content['config']['framesX'],
            (int) $content['config']['framesY'],
        ));
        // $tileset = $this->tileMerger->merge($tiles);

        return new JsonResponse([]);
    }
}
