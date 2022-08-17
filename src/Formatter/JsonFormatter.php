<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Formatter;

use PrivatePackagist\VendorDataExporter\Model;
use PrivatePackagist\VendorDataExporter\RegistryInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JsonFormatter implements FormatterInterface
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /** @param Model\Customer[] $customers */
    public function displayFromRegistry(RegistryInterface $registry, array $customers): void
    {
        $json = json_encode(array_map(fn (Model\Customer $customer): array => [
            'identifier' => $customer->slug,
            'name' => $customer->name,
            'enabled' => $customer->enabled,
            'url' => $customer->url,
            'packages' => array_values(array_map(fn (Model\Access $access): array => [
                'name' => $access->package->name,
                'versions' => array_values(array_map(
                    fn (Model\Version $version): string => $version->version,
                    $registry->getPackageVersionsCustomerCanAccess($access),
                )),
            ], $customer->getPackageAccess())),
        ], $customers), \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR);

        $this->output->writeln($json);
    }

    /** @param Model\Customer[] $customers */
    public function displayFromModels(array $customers): void
    {
        $json = json_encode(array_map(fn (Model\Customer $customer): array => [
            'identifier' => $customer->slug,
            'name' => $customer->name,
            'enabled' => $customer->enabled,
            'url' => $customer->url,
            'packages' => array_values(array_map(fn (Model\Package $package): array => [
                'name' => $package->name,
                'versions' => array_values(array_map(
                    fn (Model\Version $version): string => $version->version,
                    $package->getVersions(),
                )),
            ], $customer->getPackages())),
        ], $customers), \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR);

        $this->output->writeln($json);
    }
}
