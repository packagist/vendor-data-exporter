<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter;

use PrivatePackagist\ApiClient\Client;
use PrivatePackagist\ApiClient\Exception\ResourceNotFoundException;

/**
 * @phpstan-import-type CustomerShape from Model\Customer
 * @phpstan-import-type PackageShape from Model\Package
 */
class Populator implements PopulatorInterface
{
    /** @return Model\Customer[] */
    public function fetchCustomersAndPopulatePackageVersions(Client $client, RegistryInterface $registry): array
    {
        $customers = $this->fetchVendorCustomersAndTheirPackages($client, $registry);
        $this->populatePackageVersionsFromApi($client, $registry);
        return $customers;
    }

    /** @return Model\Customer[] */
    private function fetchVendorCustomersAndTheirPackages(Client $client, RegistryInterface $registry): array
    {
        // First, fetch all vendor customers from the API using the Private Packagist SDK.
        /** @var CustomerShape[] $response */
        $response = $client->customers()->all();
        $customers = array_map(fn (array $customerData): Model\Customer => Model\Customer::fromApiData($customerData), $response);
        // Next, fetch which packages each customer has access to (and what level of access they have).
        foreach ($customers as $customer) {
            try {
                /** @var PackageShape[] $response */
                $response = $client->customers()->listPackages($customer->id);
            } catch (ResourceNotFoundException $e) {
                // Very unlikely, but this could have been a race condition where the customer was fetched, and then
                // deleted before the request to fetch the customer's packages.
                // We *should* remove the customer from the list, but instead we are lazy and will just leave it in
                // with no packages.
                continue;
            }
            foreach ($response as $packageData) {
                $package = $registry->registerPackageFromApiData($packageData);
                $customer->addPackage($package, $packageData);
            }
        }
        return $customers;
    }

    private function populatePackageVersionsFromApi(Client $client, RegistryInterface $registry): void
    {
        foreach ($registry->getPackages() as $package) {
            try {
                foreach ($client->packages()->show($package->name)['versions'] ?? [] as $versionData) {
                    try {
                        $registry->registerVersionFromApiData($package, $versionData);
                    } catch (\LogicException $e) {
                        // This should never happen. You shouldn't be able to fetch packages from the registry, then for
                        // it to not be registered with the registry. That just illogical. Hence, LogicException.
                        continue;
                    }
                }
            } catch (ResourceNotFoundException) {
                // Again, very unlikely, but this could have been a race condition where the package was fetched, and
                // then deleted before the request to fetch the package's versions (or the API was down).
                // We *should* remove the package from the registry, but instead we are lazy and will just leave it in
                // with no versions.
                continue;
            }
        }
    }
}
