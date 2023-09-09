<?php

declare(strict_types=1);

namespace App\Merge\UI\Http;

use App\Files\Application\JsonFileManager;
use App\Merge\Application\ImageSizeDeterminant;
use App\Merge\Application\ImageSizeOptions;
use App\Merge\Application\MergeOptions;
use App\Merge\Application\TileMerger;
use App\Tiles\Application\OrderConfig;
use App\Tiles\Application\TilesOrderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class MergePixiAnimationTileset extends AbstractController
{
    public function __construct(
        private readonly ImageSizeDeterminant $imageSizeDeterminant,
        private readonly JsonFileManager $jsonFileManager,
        private readonly TileMerger $tileMerger,
        private readonly string $tilesetsPath,
    ) {
    }

    #[Route('/api/tileset/merge/{id}', name: 'merge_pixi_animation_tileset', methods: ['POST'])]
    public function __invoke(Request $request, $id)
    {
        $content = json_decode($request->getContent(), true);

        $metadata = $this->jsonFileManager->load(sprintf('%s/%s/%s', $this->tilesetsPath, $id, '/metadata/metadata.json'));

        $size = $this->imageSizeDeterminant->determine($content['tileset']['tiles'], new ImageSizeOptions(
            (int) $metadata['tileWidth'],
            (int) $metadata['tileHeight'],
        ));

        $tilesetImagick = $this->tileMerger->merge($content['tileset']['tiles'], new MergeOptions(
            $size->width,
            $size->height,
            (int) $metadata['tileWidth'],
            (int) $metadata['tileHeight'],
            (int) $content['config']['framesX'],
            (int) $content['config']['framesY'],
        ), sprintf('%s/%s/%s', $this->tilesetsPath, $id, '/tileset.png'));

        return new BinaryFileResponse($tilesetImagick->getImageFilename());
    }
}
