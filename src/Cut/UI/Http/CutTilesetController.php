<?php

declare(strict_types=1);

namespace App\Cut\UI\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class CutTilesetController
{
    #[Route('/cut-tileset', name: 'cut_tileset')]
    public function __invoke(): JsonResponse
    {
        $scannedDir = scandir('/application/src/resources/result');
        $scannedDir = array_diff($scannedDir, ['..', '.']);
        $scannedDir = array_map(fn ($file) => 'tiles/' . $file, $scannedDir);
        $scannedDir = array_values($scannedDir);

        return new JsonResponse($scannedDir);
    }
}
