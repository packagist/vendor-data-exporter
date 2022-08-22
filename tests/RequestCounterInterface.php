<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Test;

interface RequestCounterInterface
{
    public function getRequestCount(): int;
}
