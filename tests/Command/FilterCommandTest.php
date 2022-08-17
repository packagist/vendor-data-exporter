<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Test\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use PrivatePackagist\VendorDataExporter\Command\FilterCommand;
use PrivatePackagist\VendorDataExporter\Test\TestCase;

class FilterCommandTest extends TestCase
{
    private const MOCK_HTTP_REQUEST_SET = 'filter';

    private Command $command;

    public function setUp(): void
    {
        $this->command = new FilterCommand;
        $this->command->setPackagistApiClient($this->packagistApiClient);
    }

    public function testTextOutput(): void
    {
        $input = new StringInput('--format=txt');
        $output = new BufferedOutput;

        $this->httpClient->useRequestSet(self::MOCK_HTTP_REQUEST_SET);
        $this->command->run($input, $output);

        static::assertSame(25, $this->httpClient->getRequestCount());
        static::assertSame(file_get_contents(__DIR__ . '/../res/output/output.txt'), $output->fetch());
    }

    public function testJsonOutput(): void
    {
        $input = new StringInput('--format=json');
        $output = new BufferedOutput;

        $this->httpClient->useRequestSet(self::MOCK_HTTP_REQUEST_SET);
        $this->command->run($input, $output);

        static::assertSame(25, $this->httpClient->getRequestCount());
        static::assertSame(file_get_contents(__DIR__ . '/../res/output/output.json'), $output->fetch());
    }

    public function testCsvOutput(): void
    {
        $input = new StringInput('--format=csv');
        $output = new BufferedOutput;

        $this->httpClient->useRequestSet(self::MOCK_HTTP_REQUEST_SET);
        $this->command->run($input, $output);

        static::assertSame(25, $this->httpClient->getRequestCount());
        static::assertSame(file_get_contents(__DIR__ . '/../res/output/output.csv'), $output->fetch());
    }
}
