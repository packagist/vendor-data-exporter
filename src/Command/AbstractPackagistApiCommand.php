<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Command;

use PrivatePackagist\ApiClient\Client as PackagistApiClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractPackagistApiCommand extends Command
{
    private ?PackagistApiClient $packagistApiClient = null;

    protected function getPackagistApiClient(InputInterface $input): PackagistApiClient
    {
        if ($this->packagistApiClient !== null) {
            return $this->packagistApiClient;
        }

        if (!is_string($token = $input->getOption('token'))) {
            $token = $_ENV['PACKAGIST_API_TOKEN'] ?? throw new \InvalidArgumentException('Missing API credentials: provide API token via command flag or environment variable.');
        }
        if (!is_string($secret = $input->getOption('secret'))) {
            $secret = $_ENV['PACKAGIST_API_SECRET'] ?? throw new \InvalidArgumentException('Missing API credentials: provide API secret via command flag or environment variable.');
        }

        $this->packagistApiClient = new PackagistApiClient(null, $_ENV['PACKAGIST_API_URL'] ?? null);
        $this->packagistApiClient->authenticate($token, $secret);
        return $this->packagistApiClient;
    }

    /** @test */
    public function setPackagistApiClient(PackagistApiClient $client): void
    {
        $this->packagistApiClient = $client;
    }
}
