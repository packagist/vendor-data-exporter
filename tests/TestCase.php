<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Test;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PrivatePackagist\ApiClient\Client as PackagistApiClient;
use PrivatePackagist\ApiClient\HttpClient\HttpPluginClientBuilder;
use Psr\Http\Client\ClientInterface;

class TestCase extends PHPUnitTestCase
{
    protected readonly PackagistApiClient $packagistApiClient;

    public function __construct(
        protected readonly ClientInterface&RequestCounterInterface $httpClient = new MockHttpClient,
    ) {
        $this->packagistApiClient = new PackagistApiClient(new HttpPluginClientBuilder($this->httpClient));
        parent::__construct();
    }
}
