<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Command;

use PrivatePackagist\ApiClient\Client as PackagistSdk;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
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
            ->addOption('token', null, InputOption::VALUE_REQUIRED, 'Private Packagist API Token')
            ->addOption('secret', null, InputOption::VALUE_REQUIRED, 'Private Packagist API Secret')
            ->setDescription('List package versions that a vendor\'s customers have access to.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = $this->getPackagistApiClient($input);

        return 0;
    }

    private function getPackagistApiClient(InputInterface $input): PackagistSdk
    {
        if (!is_string($token = $input->getOption('token'))) {
            $token = $_ENV['PACKAGIST_API_TOKEN'] ?? throw new \InvalidArgumentException('Missing API credentials: provide API token via command flag or environment variable.');
        }
        if (!is_string($secret = $input->getOption('secret'))) {
            $secret = $_ENV['PACKAGIST_API_SECRET'] ?? throw new \InvalidArgumentException('Missing API credentials: provide API secret via command flag or environment variable.');
        }

        $apiClient = new PackagistSdk(null, $_ENV['PACKAGIST_API_URL'] ?? null);
        $apiClient->authenticate($token, $secret);

        return $apiClient;
    }
}
