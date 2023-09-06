<?php

declare(strict_types=1);

namespace App\Cut\Command;

use App\Cut\Application\TileCutter;
use App\Cut\Command\Domain\CutIntoPartsContext;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

#[AsCommand(
    name: 'tileset:cut'
)]
final class CutImageIntoPartsCommand extends Command
{
    public function __construct(private readonly TileCutter $tileCutter)
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
        $inputPath = $input->getArgument('inputPath');

        $this->tileCutter->cut(
            $inputPath,
            '/application/src/resources/result',
            CutIntoPartsContext::fromArray($input->getArguments())
        );

        return Command::SUCCESS;
    }
}
