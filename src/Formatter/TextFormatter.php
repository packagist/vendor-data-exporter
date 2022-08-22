<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Formatter;

use PrivatePackagist\VendorDataExporter\Model;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class TextFormatter implements FormatterInterface
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /** @param Model\Customer[] $customers */
    public function display(array $customers): void
    {
        $table = new Table($this->output);
        $table->setHeaderTitle('Vendor Customers and Package Versions');
        $table->setHeaders(['Customer Name', 'Customer Identifier', 'Package Name', 'Version', 'Version (Normalized)']);

        foreach ($customers as $customer) {
            foreach ($customer->getPackages() as $package) {
                foreach ($package->getVersions() as $version) {
                    $table->addRow([$customer->name, $customer->slug, $package->name, $version->version, $version->normalized]);
                }
            }
        }

        $table->render();
    }
}
