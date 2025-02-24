<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Collector;

use LogicException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\Collector\ClassNameRegexCollector;
use Qossmic\Deptrac\Collector\Registry;

final class ClassNameRegexCollectorTest extends TestCase
{
    public function dataProviderSatisfy(): iterable
    {
        yield [['regex' => '/^Foo\\\\Bar$/i'], 'Foo\\Bar', true];
        yield [['regex' => '/^Foo\\\\Bar$/i'], 'Foo\\Baz', false];
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $className, bool $expected): void
    {
        $stat = (new ClassNameRegexCollector())->satisfy(
            $configuration,
            new AstClassReference(ClassLikeName::fromFQCN($className)),
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );

        self::assertEquals($expected, $stat);
    }

    public function testWrongRegexParam(): void
    {
        $this->expectException(LogicException::class);

        (new ClassNameRegexCollector())->satisfy(
            ['Foo' => 'a'],
            new AstClassReference(ClassLikeName::fromFQCN('Foo')),
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );
    }

    public function testInvalidRegexParam(): void
    {
        $this->expectException(LogicException::class);

        (new ClassNameRegexCollector())->satisfy(
            ['regex' => '/'],
            new AstClassReference(AstMap\ClassLikeName::fromFQCN('Foo')),
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );
    }
}
