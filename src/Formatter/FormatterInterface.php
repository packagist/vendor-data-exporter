<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Formatter;

use PrivatePackagist\VendorDataExporter\Model\Customer;
use PrivatePackagist\VendorDataExporter\RegistryInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface FormatterInterface
{
    public function __construct(OutputInterface $output);

    /** @param Customer[] $customers */
    public function display(RegistryInterface $registry, array $customers): void;
}
