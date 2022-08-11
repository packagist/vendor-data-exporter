<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Util;

use Composer\Semver\Semver;
use PrivatePackagist\VendorDataExporter\Model;

class CustomerVersionFilter
{
    public function __construct(
        private readonly Model\Access $access,
    ) {}

    public function __invoke(Model\Version $version): bool
    {
        return ($this->access->constraints->expirationDate === null || $version->releasedAt === null || $this->access->constraints->expirationDate >= $version->releasedAt)
            && ($this->access->constraints->minimumStability === null || VersionParser::stabilityGreaterThanOrEqualTo($version->normalised, $this->access->constraints->minimumStability))
            && ($this->access->constraints->version === null || Semver::satisfies($version->normalised, $this->access->constraints->version));
    }
}
