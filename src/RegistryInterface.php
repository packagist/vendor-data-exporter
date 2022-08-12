<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter;

/**
 * @phpstan-import-type PackageShape from Model\Package
 * @phpstan-import-type VersionShape from Model\Version
 */
interface RegistryInterface
{
    /** @param PackageShape $data */
    public function registerPackageFromApiData(array $data): Model\Package;
    /** @param VersionShape $data */
    public function registerVersionFromApiData(Model\Package $package, array $data): Model\Version;
    /** @return array<string, Model\Package> */
    public function getPackages(): array;
    /** @return array<string, Model\Version> */
    public function getVersionsForPackage(Model\Package $package): array;
    /** @return array<string, Model\Version> */
    public function getPackageVersionsCustomerCanAccess(Model\Access $access): array;
}
