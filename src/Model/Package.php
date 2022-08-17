<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Model;

/**
 * @phpstan-type PackageShape array{name: string}
 */
class Package
{
    /** @var Version[] */
    private array $versions = [];

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

    public function addVersion(Version $version): Version
    {
        if ($version->package->name !== $this->name) {
            throw new \InvalidArgumentException(sprintf(
                'Version "%s:%s" does not belong to package "%s".',
                $version->package->name,
                $version->version,
                $this->name,
            ));
        }
        return $this->versions[$version->normalised] ??= $version;
    }

    /**
     * @return Version[]
     */
    public function getVersions(): array
    {
        return $this->versions;
    }
}
