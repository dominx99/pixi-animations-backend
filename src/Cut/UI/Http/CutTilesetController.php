<?php

declare(strict_types=1);

namespace App\Cut\UI\Http;

use App\Cut\Application\TileCutter;
use App\Cut\Command\Domain\CutIntoPartsContext;
use App\Files\Application\JsonFileManager;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CutTilesetController extends AbstractController
{
    public function __construct(
        private readonly JsonFileManager $jsonFileManager,
        private readonly TileCutter $tileCutter,
        private readonly string $tilesetsPath
    ) {
    }

    #[Route('/api/cut-tileset', name: 'api_cut_tileset')]
    public function __invoke(Request $request): JsonResponse
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('tileset');
        $tileWidth = $request->request->getInt('tileWidth');
        $tileHeight = $request->request->getInt('tileHeight');
        $id = Uuid::uuid4()->toString();

        mkdir(sprintf('%s/%s', $this->tilesetsPath, $id));
        mkdir(sprintf('%s/%s/%s', $this->tilesetsPath, $id, 'metadata'));

        $metadata = $this->tileCutter->cut(
            $file->getRealPath(),
            sprintf('%s/%s', $this->tilesetsPath, $id),
            CutIntoPartsContext::new($tileWidth, $tileHeight),
        );

        $this->jsonFileManager->save(
            sprintf('%s/%s/%s', $this->tilesetsPath, $id, 'metadata/metadata.json'),
            $metadata->toArray()
        );

        return new JsonResponse([
            'id' => $id,
        ]);
    }
}
