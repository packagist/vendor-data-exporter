<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Model;

class Access
{
    public function __construct(
        public readonly Package $package,
        public readonly Constraint $constraints,
    ) {}
}
