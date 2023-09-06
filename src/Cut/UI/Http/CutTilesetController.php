<?php

declare(strict_types=1);

namespace App\Cut\UI\Http;

use App\Files\Application\JsonFileManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class CutTilesetController extends AbstractController
{
    public function __construct(private readonly JsonFileManager $jsonFileManager)
    {
    }

    #[Route('/api/cut-tileset', name: 'cut_tileset')]
    public function __invoke(): JsonResponse
    {
        $data = $this->jsonFileManager->load('/application/src/resources/metadata/metadata.json');
        $data['tiles'] = array_map(fn ($item) => ([
            ...$item,
            'path' => 'https://tilesets.docker.localhost/tiles/' . $item['path'],
        ]), $data['tiles']);

        return new JsonResponse($data);
    }
}
