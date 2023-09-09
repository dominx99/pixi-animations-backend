<?php

declare(strict_types=1);

namespace App\Tiles\UI\Http;

use App\Files\Application\JsonFileManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class LoadTilesetController extends AbstractController
{
    public function __construct(
        private string $tilesetsPath,
        private string $websiteUrl,
        private JsonFileManager $jsonFileManager,
    ) {
    }

    #[Route('/api/tilesets/{id}', name: 'api_tilesets_load', methods: ['GET'])]
    public function __invoke(string $id): JsonResponse
    {
        $data = $this->jsonFileManager->load(sprintf('%s/%s/%s', $this->tilesetsPath, $id, 'metadata/metadata.json'));
        $data['tiles'] = array_map(fn ($item) => ([
            ...$item,
            'path' => sprintf('%s/%s/%s', 'tilesets', $id, $item['path']),
            'url' => sprintf('%s/%s/%s/%s', $this->websiteUrl, 'tilesets', $id, $item['path']),
        ]), $data['tiles']);

        return new JsonResponse($data);
    }
}
