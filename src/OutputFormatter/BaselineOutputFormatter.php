<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\OutputFormatter;

use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\RulesetEngine\Context;
use Qossmic\Deptrac\RulesetEngine\SkippedViolation;
use Qossmic\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Yaml\Yaml;
use function array_values;
use function ksort;
use function sort;

final class BaselineOutputFormatter implements OutputFormatterInterface
{
    private const DEFAULT_PATH = './deptrac.baseline.yaml';

    public static function getName(): string
    {
        return 'baseline';
    }

    public static function getConfigName(): string
    {
        return self::getName();
    }

    public function finish(
        Context $context,
        Output $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $groupedViolations = $this->collectViolations($context);

        foreach ($groupedViolations as &$violations) {
            sort($violations);
        }

        ksort($groupedViolations);
        $baselineFile = $outputFormatterInput->getOutputPath() ?? self::DEFAULT_PATH;
        $dirname = dirname($baselineFile);
        if (!is_dir($dirname) && mkdir($dirname.'/', 0777, true) && !is_dir($dirname)) {
            $output->writeLineFormatted('<error>Unable to create '.realpath($baselineFile).'</error>');

            return;
        }
        file_put_contents(
            $baselineFile,
            Yaml::dump(
                [
                    'parameters' => [
                        'skip_violations' => $groupedViolations,
                    ],
                ],
                4,
                2
            )
        );
        $output->writeLineFormatted('<info>Baseline dumped to '.realpath($baselineFile).'</info>');
    }

    /**
     * @return array<string,array<string>>
     */
    private function collectViolations(Context $context): array
    {
        $violations = [];
        foreach ($context->rules() as $rule) {
            if (!$rule instanceof Violation && !$rule instanceof SkippedViolation) {
                continue;
            }
            $dependency = $rule->getDependency();
            $dependantClass = $dependency->getDependant()->toString();
            $dependencyClass = $dependency->getDependee()->toString();

            if (!array_key_exists($dependantClass, $violations)) {
                $violations[$dependantClass] = [];
            }

            $violations[$dependantClass][$dependencyClass] = $dependencyClass;
        }

        return array_map(
            static function (array $dependencies): array {
                return array_values($dependencies);
            },
            $violations
        );
    }
}
