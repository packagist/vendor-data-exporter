<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Model;

/**
 * @phpstan-type PackageShape array{name: string}
 */
class Package
{
    protected function __construct(
        public readonly string $name,
    ) {}

    /**
     * @param PackageShape $data
     */
    public static function fromApiData(array $data): Package
    {
        return new self(
            $data['name'],
        );
    }
}
