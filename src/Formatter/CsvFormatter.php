<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Formatter;

use League\Csv\Writer;
use PrivatePackagist\VendorDataExporter\Model;
use PrivatePackagist\VendorDataExporter\RegistryInterface;
use PrivatePackagist\VendorDataExporter\Util\CustomerVersionFilter;
use Symfony\Component\Console\Output\OutputInterface;

class CsvFormatter implements FormatterInterface
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /** @param Model\Customer[] $customers */
    public function display(RegistryInterface $registry, array $customers): void
    {
        $csv = Writer::createFromStream(tmpfile() ?: throw new \LogicException('System could not create temporary file for CSV.'));
        $csv->setFlushThreshold(100);
        $csv->insertOne(['Customer Name', 'Customer Identifier', 'Package Name', 'Version', 'Version (Normalized)']);

        foreach ($customers as $customer) {
            foreach ($customer->getPackageAccess() as $access) {
                $package = $access->package;
                $versionsCustomerHasAccessTo = array_filter(
                    $registry->getVersionsForPackage($package),
                    new CustomerVersionFilter($access),
                );
                foreach ($versionsCustomerHasAccessTo as $version) {
                    $csv->insertOne([
                        $customer->name,
                        $customer->slug,
                        $package->name,
                        $version->version,
                        $version->normalised,
                    ]);
                }
            }
        }

        $this->output->write($csv->toString());
    }
}
