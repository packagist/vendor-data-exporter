<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Test;

interface RequestSetCounterInterface
{
    public function useRequestSet(?string $setName = null): void;
    public function getRequestCount(): int;
}
