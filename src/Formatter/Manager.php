<?php declare(strict_types=1);

namespace PrivatePackagist\VendorDataExporter\Formatter;

use Symfony\Component\Console\Output\OutputInterface;

class Manager implements ManagerInterface
{
    /** @var non-empty-array<string, class-string<FormatterInterface>> */
    protected const DEFAULT_FORMATTERS = [
        'txt' => TextFormatter::class,
        'json' => JsonFormatter::class,
        'csv' => CsvFormatter::class,
    ];

    /** @param non-empty-array<string, class-string<FormatterInterface>> $formatters */
    public function __construct(
        private array $formatters = self::DEFAULT_FORMATTERS,
    ) {}

    public function getFormatter(OutputInterface $output, string $type): FormatterInterface
    {
        if (!is_a($this->formatters[$type] ?? '', FormatterInterface::class, true)) {
            throw new \InvalidArgumentException(sprintf('Formatter of type "%s" not registered.', $type));
        }

        $class = $this->formatters[$type];
        return new $class($output);
    }

    /** @return non-empty-array<int, string> */
    public function getValidFormats(): array
    {
        return array_keys($this->formatters);
    }
}
