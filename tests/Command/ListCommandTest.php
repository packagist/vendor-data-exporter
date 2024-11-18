<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Test\Command;

use Http\Mock\Client as MockHttpClient;
use PHPUnit\Framework\TestCase;
use PrivatePackagist\ApiClient\Client as PackagistApiClient;
use PrivatePackagist\ApiClient\HttpClient\HttpPluginClientBuilder;
use PrivatePackagist\VendorDataExporter\Command\ListCommand;
use PrivatePackagist\VendorDataExporter\Test\MockedResponseHttpClientTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ListCommandTest extends TestCase
{
    use MockedResponseHttpClientTrait;

    private MockHttpClient $httpClient;
    private Command $command;

    public function setUp(): void
    {
        $this->httpClient = $this->createMockedResponseHttpClientUsingDirectory(__DIR__ . '/../res/http');

        $this->command = new ListCommand;
        $this->command->setPackagistApiClient(new PackagistApiClient(new HttpPluginClientBuilder($this->httpClient)));
    }

    public function testTextOutput(): void
    {
        $input = new StringInput('--format=txt');
        $output = new BufferedOutput;

        $this->command->run($input, $output);

        static::assertCount(49, $this->httpClient->getRequests());
        static::assertStringEqualsFile(__DIR__ . '/../res/output/output.txt', $output->fetch());
    }

    public function testJsonOutput(): void
    {
        $input = new StringInput('--format=json');
        $output = new BufferedOutput;

        $this->command->run($input, $output);

        static::assertCount(49, $this->httpClient->getRequests());
        static::assertStringEqualsFile(__DIR__ . '/../res/output/output.json', $output->fetch());
    }

    public function testCsvOutput(): void
    {
        $input = new StringInput('--format=csv');
        $output = new BufferedOutput;

        $this->command->run($input, $output);

        static::assertCount(49, $this->httpClient->getRequests());
        static::assertStringEqualsFile(__DIR__ . '/../res/output/output.csv', $output->fetch());
    }
}
