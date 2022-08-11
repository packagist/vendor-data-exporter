<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Formatter;

use PrivatePackagist\VendorDataExporter\Model;
use PrivatePackagist\VendorDataExporter\RegistryInterface;
use PrivatePackagist\VendorDataExporter\Util\CustomerVersionFilter;
use Symfony\Component\Console\Output\OutputInterface;

class JsonFormatter implements FormatterInterface
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /** @param Model\Customer[] $customers */
    public function display(RegistryInterface $registry, array $customers): void
    {
        $json = json_encode(array_map(fn (Model\Customer $customer): array => [
            'identifier' => $customer->slug,
            'name' => $customer->name,
            'enabled' => $customer->enabled,
            'url' => $customer->url,
            'packages' => array_values(array_map(fn (Model\Access $access): array => [
                'name' => $access->package->name,
                'versions' => array_values(array_map(fn (Model\Version $version): string => $version->version, array_filter(
                    $registry->getVersionsForPackage($access->package),
                    new CustomerVersionFilter($access),
                ))),
            ], $customer->getPackageAccess())),
        ], $customers), \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR);

        $this->output->writeln($json);
    }
}
