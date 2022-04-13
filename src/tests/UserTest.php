#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Core\BC\Domain\User;
use Kernel\Domain\ValueObjects\Identity;
use PHPUnit\Framework\TestCase;
// use Deminy\Counit\TestCase;

final class UserTest extends TestCase
{
    /**
     * @test
     **/
    public function testRegisterUserIsAUserInstance(): void
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

    /**
     * @test
     **/
    public function testApproveUser(): void
    {
        $id = (Identity::create())->toPrimitive();

        $user = User::register(
            userId: $id,
            email: 'testEmail@fakeDomain.cc',
            username: 'fakeUserName',
        );

        $user->approveUser($id);

        self::assertTrue(array_reduce($user->getRecordedEvents(), function () use ($user) {
            foreach ($user->getRecordedEvents() as $item) {
                if ($item instanceof \App\Core\BC\Domain\Events\UserHasBeenApproved) {
                    return true;
                    break;
                }
            }
            return false;
        }));
    }

    /**
     * @test
     **/
    public function testDoNotApproveUserInInvalidState(): void
    {
        $this->expectException(\Kernel\Domain\CustomAssertionFailedException::class);
        $this->expectErrorMessageMatches('/\buser.invalidStatusCondition\b/');

        $id = (Identity::create())->toPrimitive();

        $user = User::register(
            userId: $id,
            email: 'testEmail@fakeDomain.cc',
            username: 'fakeUserName',
        );

        $user->approveUser($id);
        // Approve again with status already modified
        $user->approveUser($id);
    }
}
