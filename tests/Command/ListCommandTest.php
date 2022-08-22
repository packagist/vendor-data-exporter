<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Test\Command;

use GuzzleHttp\Psr7\Response;
use Http\Message\RequestMatcher;
use Http\Mock\Client as MockHttpClient;
use PHPUnit\Framework\TestCase;
use PrivatePackagist\ApiClient\Client as PackagistApiClient;
use PrivatePackagist\ApiClient\HttpClient\HttpPluginClientBuilder;
use PrivatePackagist\VendorDataExporter\Command\ListCommand;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ListCommandTest extends TestCase
{
    protected readonly PackagistApiClient $packagistApiClient;
    protected readonly MockHttpClient $httpClient;
    private Command $command;

    public function setUp(): void
    {
        $this->httpClient = $this->createMockHttpClientUsingSavedApiResponsesFromDirectory(__DIR__ . '/../res/http');
        $this->packagistApiClient = new PackagistApiClient(new HttpPluginClientBuilder($this->httpClient));

        $this->command = new ListCommand;
        $this->command->setPackagistApiClient($this->packagistApiClient);
    }

    private function createMockHttpClientUsingSavedApiResponsesFromDirectory(string $responsesDirectory): MockHttpClient
    {
        $httpClient = new MockHttpClient;
        $httpClient->on(new class implements RequestMatcher {
            public function matches(RequestInterface $request): bool {
                return strtoupper($request->getMethod()) === 'GET';
            }
        }, function (RequestInterface $request) use ($responsesDirectory): ResponseInterface {
            $requestIdentifier = sprintf('%s %s', $request->getMethod(), $request->getUri()->getPath());
            $httpResponseFilepath = sprintf('%s/%s.json', $responsesDirectory, (new AsciiSlugger)->slug($requestIdentifier)->lower()->toString());
            if (!file_exists($httpResponseFilepath) || !is_readable($httpResponseFilepath) || !is_string($mockHttpResponse = file_get_contents($httpResponseFilepath))) {
                static::fail(sprintf('Mock response not found for "%s"; tried looking for file "%s".', $requestIdentifier, $httpResponseFilepath));
            }
            return new Response(200, ['Content-Type' => 'application/json'], $mockHttpResponse);
        });
        return $httpClient;
    }

    public function testTextOutput(): void
    {
        $input = new StringInput('--format=txt');
        $output = new BufferedOutput;

        $this->command->run($input, $output);

        static::assertCount(49, $this->httpClient->getRequests());
        static::assertSame(file_get_contents(__DIR__ . '/../res/output/output.txt'), $output->fetch());
    }

    public function testJsonOutput(): void
    {
        $input = new StringInput('--format=json');
        $output = new BufferedOutput;

        $this->command->run($input, $output);

        static::assertCount(49, $this->httpClient->getRequests());
        static::assertSame(file_get_contents(__DIR__ . '/../res/output/output.json'), $output->fetch());
    }

    public function testCsvOutput(): void
    {
        $input = new StringInput('--format=csv');
        $output = new BufferedOutput;

        $this->command->run($input, $output);

        static::assertCount(49, $this->httpClient->getRequests());
        static::assertSame(file_get_contents(__DIR__ . '/../res/output/output.csv'), $output->fetch());
    }
}
