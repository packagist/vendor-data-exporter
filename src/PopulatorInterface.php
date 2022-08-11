<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter;

use PrivatePackagist\ApiClient\Client as PackagistSdk;

interface PopulatorInterface
{
    /** @return Model\Customer[] */
    public function fetchCustomersAndPopulatePackageVersions(PackagistSdk $client, RegistryInterface $registry): array;
}
