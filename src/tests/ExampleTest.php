#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Core\ExampleBoundedContext\Domain\ExampleDomain\Events\ExampleWasCreated;
use App\Core\ExampleBoundedContext\Domain\ExampleDomain\Example;
use Boilerwork\Domain\ValueObjects\Identity;
use PHPUnit\Framework\TestCase;
// use Deminy\Counit\TestCase;

final class ExampleTest extends TestCase
{

    public function exampleProvider(): iterable
    {
        yield [
            Example::create(
                exampleId: (Identity::create())->toPrimitive(),
                name: 'Hermiston Inc.',
                region: 'EU',
            )
        ];
    }

    /**
     * @test
     * @dataProvider exampleProvider
     * @covers \App\Core\ExampleBoundedContext\Domain\ExampleDomain\Example
     * @covers \App\Core\ExampleBoundedContext\Domain\ExampleDomain\Events\ExampleWasCreated
     **/
    public function testExample(Example $example): void
    {
        $events = $example->getRecordedEvents();

        $this->assertInstanceOf(
            Example::class,
            $example
        );

        $this->assertInstanceOf(ExampleWasCreated::class, $events[0]);
        $this->assertEquals(1, $example->currentVersion());
    }
}
