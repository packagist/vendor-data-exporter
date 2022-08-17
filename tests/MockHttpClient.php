<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Test;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

class MockHttpClient implements ClientInterface, RequestSetCounterInterface
{
    private int $requestCount = 0;

    private ?string $requestSet = null;

    public function __construct(
        private readonly SluggerInterface $slugger = new AsciiSlugger,
    ) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requestCount++;
        $requestIdentifier = sprintf('%s %s', $request->getMethod(), $request->getUri()->getPath());
        $mockHttpResponse = file_get_contents($httpResponseFilepath = sprintf(
            '%s/res/http/%s%s.json',
            __DIR__,
            $this->getRequestSetSubDirectory(),
            $this->slugger->slug($requestIdentifier)->lower()->toString(),
        ));

        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            is_string($mockHttpResponse) ? $mockHttpResponse : throw new \RuntimeException(sprintf(
                'Mock response not found for "%s". Tried looking for file "%s".',
                $requestIdentifier,
                $httpResponseFilepath,
            )),
        );
    }

    public function useRequestSet(?string $setName = null): void
    {
        $this->requestSet = $setName;
    }

    public function getRequestCount(): int
    {
        return $this->requestCount;
    }

    private function getRequestSetSubDirectory(): string
    {
        return $this->requestSet !== null
            ? $this->requestSet . '/'
            : '';
    }
}
