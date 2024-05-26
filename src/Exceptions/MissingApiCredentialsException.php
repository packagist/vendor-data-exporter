<?php

declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Exceptions;

use InvalidArgumentException;

final class MissingApiCredentialsException extends InvalidArgumentException
{
    public static function missingApiToken(): self
    {
        return new self('Missing API credentials: provide API token via command flag or environment variable.');
    }

    public static function missingApiSecret(): self
    {
        return new self('Missing API credentials: provide API secret via command flag or environment variable.');
    }
}
