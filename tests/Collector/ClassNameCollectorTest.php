<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Collector;

use LogicException;
use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\AstRunner\AstMap\AstClassReference;
use Qossmic\Deptrac\Collector\ClassNameCollector;
use Qossmic\Deptrac\Collector\Registry;

final class ClassNameCollectorTest extends TestCase
{
    public function dataProviderSatisfy(): iterable
    {
        yield [['regex' => 'a'], 'foo\bar', true];
        yield [['regex' => 'a'], 'foo\bbr', false];
    }

    /**
     * @dataProvider dataProviderSatisfy
     */
    public function testSatisfy(array $configuration, string $className, bool $expected): void
    {
        $stat = (new ClassNameCollector())->satisfy(
            $configuration,
            new AstClassReference(AstMap\ClassLikeName::fromFQCN($className)),
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );

        self::assertEquals($expected, $stat);
    }

    public function testWrongRegexParam(): void
    {
        $this->expectException(LogicException::class);

        (new ClassNameCollector())->satisfy(
            ['Foo' => 'a'],
            new AstClassReference(AstMap\ClassLikeName::fromFQCN('Foo')),
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );
    }

    public function testInvalidRegexParam(): void
    {
        $this->expectException(LogicException::class);

        (new ClassNameCollector())->satisfy(
            ['regex' => '/'],
            new AstClassReference(AstMap\ClassLikeName::fromFQCN('Foo')),
            $this->createMock(AstMap::class),
            $this->createMock(Registry::class)
        );
    }
}
