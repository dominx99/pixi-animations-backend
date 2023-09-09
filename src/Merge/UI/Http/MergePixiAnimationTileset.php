<?php

declare(strict_types=1);

namespace App\Merge\UI\Http;

use App\Files\Application\JsonFileManager;
use App\Merge\Application\ImageSizeDeterminant;
use App\Merge\Application\ImageSizeOptions;
use App\Merge\Application\MergeOptions;
use App\Merge\Application\MergeTilesetOptions;
use App\Merge\Application\TileMerger;
use App\Merge\Application\TilesetMerger;
use App\Merge\Domain\Enum\AppendType;
use App\Tiles\Application\OrderConfig;
use App\Tiles\Application\TilesOrderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use function App\Merge\Application\MergeTilesetOptions;

final class MergePixiAnimationTileset extends AbstractController
{
    public function __construct(
        private readonly ImageSizeDeterminant $imageSizeDeterminant,
        private readonly JsonFileManager $jsonFileManager,
        private readonly TileMerger $tileMerger,
        private readonly TilesetMerger $tilesetMerger,
        private readonly string $tilesetsPath,
    ) {
    }

    #[Route('/api/tileset/merge/{id}', name: 'merge_pixi_animation_tileset', methods: ['POST'])]
    public function __invoke(Request $request, $id)
    {
        $content = json_decode($request->getContent(), true);

        $metadata = $this->jsonFileManager->load(sprintf('%s/%s/%s', $this->tilesetsPath, $id, '/metadata/metadata.json'));

        $animatedTilesetSize = $this->imageSizeDeterminant->determine($content['tileset']['tiles'], new ImageSizeOptions(
            (int) $metadata['tileWidth'],
            (int) $metadata['tileHeight'],
        ));

        $staticTilesetSize = $this->imageSizeDeterminant->determineStaticTileset($content['staticTileset']['tiles'], new ImageSizeOptions(
            (int) $metadata['tileWidth'],
            (int) $metadata['tileHeight'],
        ));

        $tileset = $this->tileMerger->merge($content['tileset']['tiles'], new MergeOptions(
            $animatedTilesetSize->width,
            $animatedTilesetSize->height,
            (int) $metadata['tileWidth'],
            (int) $metadata['tileHeight'],
            (int) $content['config']['framesX'],
            (int) $content['config']['framesY'],
        ), sprintf('%s/%s/%s', $this->tilesetsPath, $id, '/animated-tileset.png'));

        $staticTileset = $this->tileMerger->mergeStaticTileset($content['staticTileset']['tiles'], new MergeOptions(
            $staticTilesetSize->width,
            $staticTilesetSize->height,
            (int) $metadata['tileWidth'],
            (int) $metadata['tileHeight'],
            (int) $content['config']['framesX'],
            (int) $content['config']['framesY'],
        ), sprintf('%s/%s/%s', $this->tilesetsPath, $id, '/static-tileset.png'));

        $finalTileset = $this->tilesetMerger->merge(
            [$tileset, $staticTileset],
            new MergeTilesetOptions(AppendType::VERTICAL),
            sprintf('%s/%s/%s', $this->tilesetsPath, $id, '/tileset.png')
        );

        return new BinaryFileResponse($finalTileset->getImageFilename());
    }
}
