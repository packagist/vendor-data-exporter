<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Model;

/**
 * @phpstan-type VersionShape array{version: string, versionNormalized: string, releasedAt?: string|null}
 */
class Version
{
    protected function __construct(
        public readonly string $version,
        public readonly string $normalised,
        public readonly ?\DateTimeInterface $releasedAt,
    ) {}

    /**
     * @param VersionShape $data
     */
    public static function fromApiData(array $data): self
    {
        return new self(
            $data['version'],
            $data['versionNormalized'],
            array_key_exists('releasedAt', $data) && is_string($data['releasedAt'])
                ? (new \DateTimeImmutable($data['releasedAt']))->setTimezone(new \DateTimeZone('UTC'))
                : null,
        );
    }
}
