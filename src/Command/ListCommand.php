<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    public const DEFAULT_COMMAND_NAME = 'list';

    public function __construct(
    ) {
        parent::__construct(self::DEFAULT_COMMAND_NAME);
    }

    protected function configure(): void
    {
        $this
            ->setName(self::DEFAULT_COMMAND_NAME)
            ->setDescription('List package versions that a vendor\'s customers have access to.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }
}
