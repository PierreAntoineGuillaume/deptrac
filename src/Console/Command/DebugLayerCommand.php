<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Console\Symfony\Style;
use Qossmic\Deptrac\Console\Symfony\SymfonyOutput;
use Qossmic\Deptrac\Exception\Console\InvalidLayerException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function getcwd;
use const DIRECTORY_SEPARATOR;

class DebugLayerCommand extends Command
{
    use DefaultDepFileTrait;

    public static $defaultName = 'debug:layer';
    public static $defaultDescription = 'Checks which tokens belong to the provided layer';

    private DebugLayerRunner $runner;

    public function __construct(DebugLayerRunner $runner)
    {
        $this->runner = $runner;

        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument('layer', InputArgument::OPTIONAL, 'Layer to debug');
        $this->addOption(
            'depfile',
            null,
            InputOption::VALUE_REQUIRED,
            '!deprecated: use --config-file instead - Path to the depfile',
            getcwd().DIRECTORY_SEPARATOR.'depfile.yaml'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle = new Style(new SymfonyStyle($input, $output));
        $symfonyOutput = new SymfonyOutput($output, $outputStyle);
        /** @var ?string $layer */
        $layer = $input->getArgument('layer');
        $configFile = self::getConfigFile($input, $symfonyOutput);

        $options = new DebugLayerOptions($configFile, $layer);

        try {
            $this->runner->run($options, $symfonyOutput);
        } catch (InvalidLayerException $invalidLayerException) {
            $outputStyle->error('Layer not found.');

            return 1;
        }

        return 0;
    }
}
