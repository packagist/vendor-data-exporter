<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Model;

/**
 * @phpstan-type ConstraintShape array{versionConstraint?: string|null, minimumAccessibleStability?: string|null, expirationDate?: string|null}
 */
class Constraint
{
    public function __construct(
        public readonly ?string $version,
        public readonly ?string $minimumStability,
        public readonly ?\DateTimeImmutable $expirationDate,
    ) {}
}
