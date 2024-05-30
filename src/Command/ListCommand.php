<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Command;

use PrivatePackagist\ApiClient\Client as PackagistApiClient;
use PrivatePackagist\VendorDataExporter\Exceptions\MissingApiCredentialsException;
use PrivatePackagist\VendorDataExporter\Formatter\Manager;
use PrivatePackagist\VendorDataExporter\Formatter\ManagerInterface;
use PrivatePackagist\VendorDataExporter\Model;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @phpstan-import-type CustomerShape from Model\Customer
 * @phpstan-import-type PackageShape from Model\Package
 * @phpstan-import-type VersionShape from Model\Version
 */
final class ListCommand extends Command
{
    public const DEFAULT_COMMAND_NAME = 'list';

    private ?PackagistApiClient $packagistApiClient = null;

    public function __construct(
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

        /** @var CustomerShape[] $response */
        $response = $client->customers()->all();
        $customers = array_map(fn (array $customerData): Model\Customer => Model\Customer::fromApiData($customerData), $response);
        foreach ($customers as $customer) {
            /** @var PackageShape[] $response */
            $response = $client->customers()->listPackages($customer->id);
            $packages = array_map(fn (array $packageData): Model\Package => Model\Package::fromApiData($packageData), $response);
            foreach ($packages as $package) {
                /** @var array{versions?: VersionShape[]} $response */
                $response = $client->customers()->showPackage($customer->id, $package->name);
                $versions = array_map(fn (array $versionData): Model\Version => Model\Version::fromApiData($package, $versionData), $response['versions'] ?? []);
                foreach ($versions as $version) {
                    $package->addVersion($version);
                }
                $customer->addPackage($package);
            }
        }

        $formatter = $this->outputFormatterManager->getFormatter($output, $input->getOption('format'));
        $formatter->display($customers);

        return 0;
    }

    private function getPackagistApiClient(InputInterface $input): PackagistApiClient
    {
        if ($this->packagistApiClient !== null) {
            return $this->packagistApiClient;
        }

        if (!is_string($token = $input->getOption('token'))) {
            $token = $_ENV['PACKAGIST_API_TOKEN'] ?? throw MissingApiCredentialsException::missingApiToken();
        }
        if (!is_string($secret = $input->getOption('secret'))) {
            $secret = $_ENV['PACKAGIST_API_SECRET'] ?? throw MissingApiCredentialsException::missingApiSecret();
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
