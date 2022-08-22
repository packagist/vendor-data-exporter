<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Formatter;

use PrivatePackagist\VendorDataExporter\Model;
use Symfony\Component\Console\Output\OutputInterface;

class JsonFormatter implements FormatterInterface
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /** @param Model\Customer[] $customers */
    public function display(array $customers): void
    {
        $json = json_encode(array_map(fn (Model\Customer $customer): array => [
            'identifier' => $customer->slug,
            'name' => $customer->name,
            'packages' => array_values(array_map(fn (Model\Package $package): array => [
                'name' => $package->name,
                'versions' => array_values(array_map(
                    fn (Model\Version $version): array => [
                        'version' => $version->version,
                        'normalized' => $version->normalized,
                    ],
                    $package->getVersions(),
                )),
            ], $customer->getPackages())),
        ], $customers), \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR);

        $this->output->writeln($json);
    }
}
