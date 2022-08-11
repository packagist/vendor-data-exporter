<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Model;

use PrivatePackagist\VendorDataExporter\Util\VersionParser;

/**
 * @phpstan-type ConstraintShape array{versionConstraint?: string|null, minimumAccessibleStability?: VersionParser::STABILITY_*|null, expirationDate?: string|null}
 */
class Constraint
{
    /** @param VersionParser::STABILITY_*|null $minimumStability */
    public function __construct(
        public readonly ?string $version,
        public readonly ?string $minimumStability,
        public readonly ?\DateTimeImmutable $expirationDate,
    ) {}
}
