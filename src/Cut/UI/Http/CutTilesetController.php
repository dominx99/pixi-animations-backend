<?php

declare(strict_types=1);

namespace App\Cut\UI\Http;

use App\Files\Application\JsonFileManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class CutTilesetController extends AbstractController
{
    public function __construct(private readonly JsonFileManager $jsonFileManager, private readonly string $tilesetsPath)
    {
    }

    #[Route('/api/cut-tileset', name: 'api_cut_tileset')]
    public function __invoke(): JsonResponse
    {
        $data = $this->jsonFileManager->load($this->tilesetsPath . '/metadata/metadata.json');
        $data['tiles'] = array_map(fn ($item) => ([
            ...$item,
            'path' => 'tilesets/' . $item['path'],
            'url' => 'https://tilesets.docker.localhost/tilesets/' . $item['path'],
        ]), $data['tiles']);

        return new JsonResponse($data);
    }
}
