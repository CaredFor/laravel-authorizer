<?php

namespace Benwilkins\Authorizer\Tests;


use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GateTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testGateDeniesPermission()
    {
        $user = factory(\App\User::class)->create();

        $this->assertFalse($user->can('permission1'));
    }

    public function testGateAllowsPermission()
    {
        $user = factory(\App\User::class)->create();

        $user->grantPermission('permission1')->refresh();
        $this->assertTrue($user->isGrantedPermission('permission1'));
        $this->assertTrue($user->can('permission1'));
    }

    public function testGateAllowsPermissionForTeam()
    {
        $user = factory(\App\User::class)->create();
        $team = config('authorizer.teams.model')::first();

        $user->grantPermission('permission1', $team);
        $this->assertFalse($user->can('permission1'));
        $this->assertTrue($user->can('permission1', $team));
    }
}
