<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter;

use PrivatePackagist\VendorDataExporter\Util\CustomerVersionFilter;

/**
 * @phpstan-import-type PackageShape from Model\Package
 * @phpstan-import-type VersionShape from Model\Version
 */
class Registry implements RegistryInterface
{
    /** @var array<string, Model\Package> */
    private array $packages = [];

    public function registerPackageFromApiData(array $data): Model\Package
    {
        $package = Model\Package::fromApiData($data);
        return $this->packages[$package->name] ??= $package;
    }

    public function registerVersionFromApiData(Model\Package $package, array $data): Model\Version
    {
        $package = ($this->packages[$package->name] ??= $package);
        $version = Model\Version::fromApiData($package, $data);
        $package->addVersion($version);
        return $version;
    }

    public function getPackages(): array
    {
        return $this->packages;
    }

    public function getPackageVersionsCustomerCanAccess(Model\Access $access): array
    {
        return array_filter($access->package->getVersions(), new CustomerVersionFilter($access));
    }
}
