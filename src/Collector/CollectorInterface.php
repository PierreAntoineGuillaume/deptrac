<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use Qossmic\Deptrac\AstRunner\AstMap;

/**
 * A collector is responsible to tell from an AST node (e.g. a specific class) is part of a layer.
 */
interface CollectorInterface
{
    /**
     * @param array<string, string|array<string, string>> $configuration     List of arguments passed for this collector declaration
     * @param AstMap\AstTokenReference                    $astTokenReference Token being checked
     * @param array<string, ?bool>                        $resolutionTable   layer name => is part of the layer? (NULL = Unknown)
     *
     * @example
     *  For the YAML configuration:
     *  ```yaml
     *  collectors:
     *      - type: className
     *        value: .*Controller.*
     *  ```
     *  The configuration `$configuration` will be:
     *  [
     *      'type' => 'className',
     *      'value' => '.*Controller.*',
     *  ]
     */
    public function satisfy(
        array $configuration,
        AstMap\AstTokenReference $astTokenReference,
        AstMap $astMap,
        Registry $collectorRegistry,
        array $resolutionTable = []
    ): bool;

    /**
     * @param array<string, string|array<string, string>> $configuration
     * @param array<string, ?bool>                        $resolutionTable layer name => is part of the layer? (NULL = Unknown)
     */
    public function resolvable(array $configuration, Registry $collectorRegistry, array $resolutionTable): bool;
}
