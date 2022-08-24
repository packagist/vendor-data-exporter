<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Test;

use GuzzleHttp\Psr7\Response;
use Http\Message\RequestMatcher;
use Http\Mock\Client as MockHttpClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

trait MockedResponseHttpClientTrait
{
    private function createMockedResponseHttpClientUsingDirectory(string $mockedResponsesDirectory): MockHttpClient
    {
        $httpClient = new MockHttpClient;
        $httpClient->on(new class implements RequestMatcher {
            public function matches(RequestInterface $request): bool {
                return strtoupper($request->getMethod()) === 'GET';
            }
        }, function (RequestInterface $request) use ($mockedResponsesDirectory): ResponseInterface {
            $requestIdentifier = sprintf('%s %s', $request->getMethod(), $request->getUri()->getPath());
            $httpResponseFilepath = sprintf('%s/%s.json', $mockedResponsesDirectory, (new AsciiSlugger)->slug($requestIdentifier)->lower()->toString());
            if (!file_exists($httpResponseFilepath) || !is_readable($httpResponseFilepath) || !is_string($mockHttpResponse = file_get_contents($httpResponseFilepath))) {
                throw new \RuntimeException(sprintf('Mock response not found for "%s"; tried looking for file "%s".', $requestIdentifier, $httpResponseFilepath));
            }
            return new Response(200, ['Content-Type' => 'application/json'], $mockHttpResponse);
        });
        return $httpClient;
    }
}
