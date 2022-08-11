<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Util;

use Composer\Semver\VersionParser as ComposerVersionParser;

class VersionParser extends ComposerVersionParser
{
    public const STABILITY_STABLE = 'stable';
    public const STABILITY_RC = 'RC';
    public const STABILITY_BETA = 'beta';
    public const STABILITY_ALPHA = 'alpha';
    public const STABILITY_DEV = 'dev';

    /** @var array<int, self::STABILITY_*> */
    private const ORDER_OF_STABILITY = [
        self::STABILITY_DEV,
        self::STABILITY_ALPHA,
        self::STABILITY_BETA,
        self::STABILITY_RC,
        self::STABILITY_STABLE,
    ];

    /**
     * @param self::STABILITY_* $minimumStability
     * @throws \InvalidArgumentException
     */
    public static function stabilityGreaterThanOrEqualTo(string $version, string $minimumStability): bool
    {
        $versionStabilityIndex = array_search(self::parseStability($version), self::ORDER_OF_STABILITY, true);
        $minimumStabilityIndex = array_search($minimumStability, self::ORDER_OF_STABILITY, true);
        if ($versionStabilityIndex === false || $minimumStabilityIndex === false) {
            throw new \InvalidArgumentException('Invalid stability level provided.');
        }
        return $versionStabilityIndex >= $minimumStabilityIndex;
    }
}
