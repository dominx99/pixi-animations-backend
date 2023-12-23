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
use App\Tiled\Application\TiledAnimationBuilder;
use App\Tiled\Domain\ValueObject\Image;
use App\Tiled\Domain\ValueObject\Tileset;
use App\Tiles\Domain\ValueObject\AnimatedTile;
use App\Tiles\Domain\ValueObject\ImageFrameSize;
use App\Tiles\Domain\ValueObject\Position;
use App\Tiles\Domain\ValueObject\Tile;
use App\Tiles\Domain\ValueObject\TileId;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use ZipArchive;

final class MergePixiAnimationTilesetController extends AbstractController
{
    public function __construct(
        private readonly ImageSizeDeterminant $imageSizeDeterminant,
        private readonly JsonFileManager $jsonFileManager,
        private readonly TileMerger $tileMerger,
        private readonly TilesetMerger $tilesetMerger,
        private readonly TiledAnimationBuilder $tiledAnimationBuilder,
        private readonly SerializerInterface $serializer,
        private readonly string $tilesetsPath,
    ) {
    }

    #[Route('/api/tileset/merge/{id}', name: 'merge_pixi_animation_tileset', methods: ['POST'])]
    public function __invoke(Request $request, string $id)
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

        $tilesets = [];
        $tilesets[] = $this->tileMerger->merge($content['tileset']['tiles'], new MergeOptions(
            $animatedTilesetSize->width,
            $animatedTilesetSize->height,
            (int) $metadata['tileWidth'],
            (int) $metadata['tileHeight'],
            (int) $content['config']['framesX'],
            (int) $content['config']['framesY'],
        ), sprintf('%s/%s/%s', $this->tilesetsPath, $id, '/animated-tileset.png'));

        if ($staticTilesetSize->width > 0 && $staticTilesetSize->height > 0) {
            $tilesets[] = $this->tileMerger->mergeStaticTileset($content['staticTileset']['tiles'], new MergeOptions(
                $staticTilesetSize->width,
                $staticTilesetSize->height,
                (int) $metadata['tileWidth'],
                (int) $metadata['tileHeight'],
                (int) $content['config']['framesX'],
                (int) $content['config']['framesY'],
            ), sprintf('%s/%s/%s', $this->tilesetsPath, $id, '/static-tileset.png'));
        }

        $finalTileset = $this->tilesetMerger->merge(
            $tilesets,
            new MergeTilesetOptions(AppendType::VERTICAL),
            sprintf('%s/%s/%s', $this->tilesetsPath, $id, '/tileset.png')
        );

        $tilesetFilename = sprintf('%s.png', $metadata['name']);
        $tsxFilename = sprintf('%s.tsx', $metadata['name']);

        $finalImageDetails = $finalTileset->identifyImage();
        $columns = $finalImageDetails['geometry']['width'] / $metadata['tileWidth'];
        $rows = $finalImageDetails['geometry']['height'] / $metadata['tileHeight'];
        $tileCount = $columns * $rows;

        $imageFrameSize = new ImageFrameSize(
            $columns,
            $rows,
            $content['config']['framesX'],
            $content['config']['framesY'],
        );

        $animatedTiles = new ArrayCollection(array_map(fn (array $animatedTile) => new AnimatedTile(
            new Position($animatedTile['x'], $animatedTile['y']),
            new ArrayCollection(
                array_map(fn (array $tile, int $key) => new Tile(
                    TileId::fromPosition(
                        new Position((int) $animatedTile['x'], (int) $animatedTile['y']),
                        $imageFrameSize,
                        $key
                    ),
                    new Position((int) $animatedTile['x'], (int) $animatedTile['y']),
                    $tile['path']
                ), $animatedTile['tiles'], array_keys($animatedTile['tiles']))
            )
        ), $content['tileset']['tiles']));

        $tileset = Tileset::new(
            image: new Image(
                source: sprintf('./%s', $tilesetFilename),
                width: $finalImageDetails['geometry']['width'],
                height: $finalImageDetails['geometry']['height'],
            ),
            name: $metadata['name'],
            tileWidth: (int) $metadata['tileWidth'],
            tileHeight: (int) $metadata['tileHeight'],
            tileCount: $tileCount,
            columns: $columns,
        );

        $tileset = $this->tiledAnimationBuilder->build($tileset, $animatedTiles);

        $xml = $this->serializer->serialize($tileset, XmlEncoder::FORMAT);

        $xmlFileName = sprintf('%s/%s/%s', $this->tilesetsPath, $id, 'tileset.tsx');

        file_put_contents($xmlFileName, $xml);
        $zipPath = sprintf(sprintf('%s/%s.zip', sys_get_temp_dir(), uniqid()));

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE);

        $zip->addFile($xmlFileName, $tsxFilename);
        $zip->addFile($finalTileset->getImageFilename(), $tilesetFilename);

        $zip->close();

        return new BinaryFileResponse($zipPath);
    }
}
