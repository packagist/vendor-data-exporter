<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Model;

use PrivatePackagist\VendorDataExporter\Util\VersionParser;

/**
 * @phpstan-type CustomerShape array{id: int, name: string, urlName: string, enabled: bool, composerRepository: array{url: string}, minimumAccessibleStability: VersionParser::STABILITY_*}
 * @phpstan-import-type ConstraintShape from Constraint
 */
class Customer
{
    /** @var Access[] */
    private array $packages = [];

    /** @param VersionParser::STABILITY_* $minimumAccessibleStability */
    protected function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly bool $enabled,
        public readonly string $url,
        public readonly string $minimumAccessibleStability,
    ) {}

    /**
     * @param CustomerShape $data
     */
    public static function fromApiData(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['urlName'],
            $data['enabled'],
            $data['composerRepository']['url'],
            $data['minimumAccessibleStability'],
        );
    }

    /**
     * @param ConstraintShape $data
     */
    public function addPackage(Package $package, array $data): void
    {
        $this->packages[$package->name] ??= new Access($package, new Constraint(
            $data['versionConstraint'] ?? null,
            $data['minimumAccessibleStability'] ?? $this->minimumAccessibleStability,
            array_key_exists('expirationDate', $data) && is_string($data['expirationDate'])
                ? (new \DateTimeImmutable($data['expirationDate']))->setTimezone(new \DateTimeZone('UTC'))
                : null,
        ));
    }

    /** @return Access[] */
    public function getPackageAccess(): array
    {
        return $this->packages;
    }
}
