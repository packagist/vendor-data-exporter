<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Test\Command;

use PHPUnit\Framework\TestCase;
use PrivatePackagist\ApiClient\Client as PackagistApiClient;
use PrivatePackagist\ApiClient\HttpClient\HttpPluginClientBuilder;
use PrivatePackagist\VendorDataExporter\Command\ListCommand;
use PrivatePackagist\VendorDataExporter\Test\MockHttpClient;
use PrivatePackagist\VendorDataExporter\Test\RequestCounterInterface;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ListCommandTest extends TestCase
{
    protected readonly PackagistApiClient $packagistApiClient;
    protected readonly ClientInterface&RequestCounterInterface $httpClient;
    private Command $command;

    public function setUp(): void
    {
        $this->httpClient = new MockHttpClient;
        $this->packagistApiClient = new PackagistApiClient(new HttpPluginClientBuilder($this->httpClient));

        $this->command = new ListCommand;
        $this->command->setPackagistApiClient($this->packagistApiClient);
    }

    public function testTextOutput(): void
    {
        $input = new StringInput('--format=txt');
        $output = new BufferedOutput;

        $this->command->run($input, $output);

        static::assertSame(49, $this->httpClient->getRequestCount());
        static::assertSame(file_get_contents(__DIR__ . '/../res/output/output.txt'), $output->fetch());
    }

    public function testJsonOutput(): void
    {
        $input = new StringInput('--format=json');
        $output = new BufferedOutput;

        $this->command->run($input, $output);

        static::assertSame(49, $this->httpClient->getRequestCount());
        static::assertSame(file_get_contents(__DIR__ . '/../res/output/output.json'), $output->fetch());
    }

    public function testCsvOutput(): void
    {
        $input = new StringInput('--format=csv');
        $output = new BufferedOutput;

        $this->command->run($input, $output);

        static::assertSame(49, $this->httpClient->getRequestCount());
        static::assertSame(file_get_contents(__DIR__ . '/../res/output/output.csv'), $output->fetch());
    }
}
