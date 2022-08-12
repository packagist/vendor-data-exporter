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

    /** @var array<string, array<string, Model\Version>> */
    private array $versions = [];

    public function registerPackageFromApiData(array $data): Model\Package
    {
        $package = Model\Package::fromApiData($data);
        $this->versions[$package->name] ??= [];
        return $this->packages[$package->name] ??= $package;
    }

    /** @throws \LogicException */
    public function registerVersionFromApiData(Model\Package $package, array $data): Model\Version
    {
        if (!array_key_exists($package->name, $this->versions)) {
            throw new \LogicException(sprintf('Cannot register version; package "%s" has not been registered.', $package->name));
        }
        $version = Model\Version::fromApiData($data);
        return $this->versions[$package->name][$version->normalised] ??= $version;
    }

    public function getPackages(): array
    {
        return $this->packages;
    }

    /** @throws \InvalidArgumentException */
    public function getVersionsForPackage(Model\Package $package): array
    {
        return $this->versions[$package->name] ?? throw new \InvalidArgumentException(sprintf('Package "%s" was not found in registry.', $package->name));
    }

    /** @throws \InvalidArgumentException */
    public function getPackageVersionsCustomerCanAccess(Model\Access $access): array
    {
        return array_filter(
            $this->versions[$access->package->name] ?? throw new \InvalidArgumentException(sprintf('Package "%s" was not found in registry.', $access->package->name)),
            new CustomerVersionFilter($access),
        );
    }
}
