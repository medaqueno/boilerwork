#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Core\BC\Domain\User;
use Kernel\Domain\ValueObjects\Identity;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    /**
     * @test */
    public function testRegisterUserIsAnUserInstance(): void
    {
        $user = User::register(
            userId: (Identity::create())->toPrimitive(),
            email: 'testEmail@fakeDomain.cc',
            username: 'fakeUserName',
        );

        self::assertInstanceOf(
            User::class,
            $user
        );
    }
}
