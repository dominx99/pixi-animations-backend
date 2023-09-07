<?php

declare(strict_types=1);

namespace App\Tiles\Application;

final class InternalTilesOrderer implements TilesOrderer
{
    /**
        * @param array $tiles
        [
            {
                x: 0,
                y: 0,
                tiles: [
                    {
                        x: 0,
                        y: 0,
                        path: '0_0.png'
                    },
                    {
                        x: 0,
                        y: 1,
                        path: '0_1.png'
                    }
                ]
            }
        ]
     */
    public function order(array $tiles, OrderConfig $config): array
    {
        $hasNextTiles = false;
        $iteration = 0;
        $resultTiles = [];

        if (count($tiles) <= 0) {
            return [];
        }

        do {
            $hasNextTiles = false;
            for ($x = 0; $x < $config->framesX; $x++) {
                for ($y = 0; $y < $config->framesY; $y++) {
                    foreach ($tiles as $tile) {
                        if ($tile['x'] === $x && $tile['y'] === $y) {
                            $moveX = 1;
                            $moveY = $config->framesY * ($iteration + 1) - $config->framesY;
                            $moveY = $moveY > 0 ? $moveY : $y;
                            if (!array_key_exists($x * $moveX, $resultTiles)) {
                                $resultTiles[$x * $moveX] = [];
                            }

                            if (!array_key_exists($iteration, $tile['tiles'])) {
                                continue;
                            }

                            $resultTiles[$x * $moveX][$y * $moveY] = $tile['tiles'][$iteration];

                            if (count($tile['tiles']) > ($iteration + 1)) {
                                $hasNextTiles = true;
                            }
                        }
                    }
                }
            }

            $iteration++;
        } while ($hasNextTiles);

        return $resultTiles;
    }
}
