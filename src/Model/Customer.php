<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Model;

/**
 * @phpstan-type CustomerShape array{id: int, name: string, urlName: string, enabled: bool, composerRepository: array{url: string}, minimumAccessibleStability: string}
 */
class Customer
{
    /** @var Package[] */
    private array $packages = [];

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

    public function addPackage(Package $package): void
    {
        $this->packages[$package->name] ??= $package;
    }

    /** @return Package[] */
    public function getPackages(): array
    {
        return $this->packages;
    }
}
