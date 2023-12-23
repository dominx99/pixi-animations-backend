<?php

declare(strict_types=1);

namespace App\Cut\UI\Http;

use App\Cut\Application\VerticalToHorizontalOptions;
use App\Cut\Application\VerticalToHorizontalTransformer;
use App\Cut\Domain\Enum\TransformationType;
use App\Files\Application\JsonFileManager;
use App\Merge\Application\ImageSizeDeterminant;
use App\Merge\Application\ImageSizeOptions;
use App\Merge\Application\MergeOptions;
use App\Merge\Application\TileMerger;
use App\Tiled\Application\TiledAnimationBuilder;
use App\Tiled\Domain\ValueObject\Image;
use App\Tiled\Domain\ValueObject\Tileset;
use App\Tiles\Application\ImagickImageSizeDeterminant;
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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use ZipArchive;

final class PredefinedTransformationController extends AbstractController
{
    public function __construct(
        private readonly JsonFileManager $jsonFileManager,
        private readonly string $tilesetsPath,
        private readonly VerticalToHorizontalTransformer $verticalToHorizontalTransformer,
        private readonly TileMerger $tileMerger,
        private readonly ImageSizeDeterminant $imageSizeDeterminant,
        private readonly TiledAnimationBuilder $tiledAnimationBuilder,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('/api/tileset/predefined/{id}', name: 'merge_pixi_animation_tileset', methods: ['POST'])]
    public function __invoke(Request $request, string $id): Response
    {
        $content = json_decode($request->getContent(), true);

        $metadata = $this->jsonFileManager->load(sprintf('%s/%s/%s', $this->tilesetsPath, $id, '/metadata/metadata.json'));
        $options = $content['config'];

        $transformationType = $content['transformationType'];

        $tilesToMerge = [];

        if ($transformationType === TransformationType::VERTICAL_TO_HORIZONTAL) {
            $tilesToMerge = $this->verticalToHorizontalTransformer->transform(
                $id,
                $metadata,
                VerticalToHorizontalOptions::fromArray($options),
            );
        }

        $imageSize = $this->imageSizeDeterminant->determine($tilesToMerge, new ImageSizeOptions(
            (int) $metadata['tileWidth'],
            (int) $metadata['tileHeight'],
        ));

        $imageDestination = sprintf('%s/%s/%s', $this->tilesetsPath, $id, '/animated-tileset.png');

        $imagickTileset = $this->tileMerger->merge($tilesToMerge, new MergeOptions(
            $imageSize->width,
            $imageSize->height,
            (int) $metadata['tileWidth'],
            (int) $metadata['tileHeight'],
            (int) $content['config']['framesX'],
            (int) $content['config']['framesY'],
        ), $imageDestination);

        $imageDetails = $imagickTileset->identifyImage();
        $columns = $imageDetails['geometry']['width'] / $metadata['tileWidth'];
        $rows = $imageDetails['geometry']['height'] / $metadata['tileHeight'];
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
        ), $tilesToMerge));

        $tsxFilename = sprintf('%s.tsx', $metadata['name']);
        $tilesetFilename = sprintf('%s.png', $metadata['name']);

        $tileset = Tileset::new(
            image: new Image(
                source: sprintf('./%s', $tilesetFilename),
                width: $imageDetails['geometry']['width'],
                height: $imageDetails['geometry']['height'],
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
        $zip->addFile($imagickTileset->getImageFilename(), $tilesetFilename);

        $zip->close();

        return new BinaryFileResponse($zipPath);
    }
}
