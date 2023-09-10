<?php

declare(strict_types=1);

namespace App\Cut\Command;

use App\Tiles\Application\TilesetSizeFixer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'tileset:fix-size',
)]
final class FixTilesetSizeCommand extends Command
{
    public function __construct(private readonly TilesetSizeFixer $tilesetSizeFixer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('inputPath', InputArgument::REQUIRED, 'Path to the input image')
            ->addArgument('tileWidth', InputArgument::REQUIRED, 'Tile width')
            ->addArgument('tileHeight', InputArgument::REQUIRED, 'Tile height')
        ;
    }


    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle  = new SymfonyStyle($input, $output);

        $logger = fn ($message) => $symfonyStyle->writeln($message);

        $path = $this->tilesetSizeFixer->fix(
            '/application/resources/worlds',
            (int) $input->getArgument('tileWidth'),
            (int) $input->getArgument('tileHeight'),
            $logger,
        );

        $symfonyStyle->writeln(sprintf('Fixed tileset size in %s', $path));

        return Command::SUCCESS;
    }
}
