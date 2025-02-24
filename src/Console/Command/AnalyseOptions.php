<?php

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Exception\Console\InvalidArgumentException;
use function is_string;

class AnalyseOptions
{
    private string $configurationFile;

    private bool $noProgress;

    private string $formatter;

    private ?string $output;

    private bool $reportSkipped;

    private bool $reportUncovered;

    private bool $failOnUncovered;

    /**
     * @param mixed $configurationFile
     */
    public function __construct(
        $configurationFile,
        bool $noProgress,
        string $formatter,
        ?string $output,
        bool $reportSkipped,
        bool $reportUncovered,
        bool $failOnUncovered
    ) {
        if (!is_string($configurationFile)) {
            throw InvalidArgumentException::invalidDepfileType($configurationFile);
        }

        $this->configurationFile = $configurationFile;
        $this->noProgress = $noProgress;
        $this->formatter = $formatter;
        $this->output = $output;
        $this->reportSkipped = $reportSkipped;
        $this->reportUncovered = $reportUncovered;
        $this->failOnUncovered = $failOnUncovered;
    }

    public function getConfigurationFile(): string
    {
        return $this->configurationFile;
    }

    public function showProgress(): bool
    {
        return !$this->noProgress;
    }

    public function getFormatter(): string
    {
        return $this->formatter;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function reportSkipped(): bool
    {
        return $this->reportSkipped;
    }

    public function reportUncovered(): bool
    {
        return $this->reportUncovered;
    }

    public function failOnUncovered(): bool
    {
        return $this->failOnUncovered;
    }
}
