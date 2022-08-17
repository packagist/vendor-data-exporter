<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Command;

use PrivatePackagist\ApiClient\Client as PackagistSdk;
use PrivatePackagist\VendorDataExporter\Formatter\Manager;
use PrivatePackagist\VendorDataExporter\Formatter\ManagerInterface;
use PrivatePackagist\VendorDataExporter\Populator;
use PrivatePackagist\VendorDataExporter\PopulatorInterface;
use PrivatePackagist\VendorDataExporter\Registry;
use PrivatePackagist\VendorDataExporter\RegistryInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends AbstractPackagistApiCommand
{
    public const DEFAULT_COMMAND_NAME = 'list';

    public function __construct(
        private readonly RegistryInterface $registry = new Registry,
        private readonly PopulatorInterface $apiModelPopulator = new Populator,
        private readonly ManagerInterface $outputFormatterManager = new Manager,
    ) {
        parent::__construct(self::DEFAULT_COMMAND_NAME);
    }

    protected function configure(): void
    {
        $this
            ->setName(self::DEFAULT_COMMAND_NAME)
            ->addOption('token', null, InputOption::VALUE_REQUIRED, 'Private Packagist API Token')
            ->addOption('secret', null, InputOption::VALUE_REQUIRED, 'Private Packagist API Secret')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'The output format (txt, json, or csv)', 'txt', fn (): array => $this->outputFormatterManager->getValidFormats())
            ->setDescription('List package versions that a vendor\'s customers have access to.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = $this->getPackagistApiClient($input);
        $customers = $this->apiModelPopulator->fetchCustomersAndPopulatePackageVersions($client, $this->registry);

        $formatter = $this->outputFormatterManager->getFormatter($output, $input->getOption('format'));
        $formatter->display($this->registry, $customers);

        return 0;
    }
}
