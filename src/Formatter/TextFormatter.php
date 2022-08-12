<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Formatter;

use PrivatePackagist\VendorDataExporter\Model;
use PrivatePackagist\VendorDataExporter\RegistryInterface;
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
    public function display(RegistryInterface $registry, array $customers): void
    {
        $table = new Table($this->output);
        $table->setHeaderTitle('Vendor Customers and Package Versions');
        $table->setHeaders(['Customer Name', 'Package Name', 'Version']);

        foreach ($customers as $customer) {
            foreach ($customer->getPackageAccess() as $access) {
                foreach ($registry->getPackageVersionsCustomerCanAccess($access) as $version) {
                    $table->addRow([$customer->name, $access->package->name, $version->version]);
                }
            }
        }

        $table->render();
    }
}
