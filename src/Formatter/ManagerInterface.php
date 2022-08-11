<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Formatter;

use Symfony\Component\Console\Output\OutputInterface;

interface ManagerInterface
{
    public function getFormatter(OutputInterface $output, string $type): FormatterInterface;
    /** @return string[] */
    public function getValidFormats(): array;
}
