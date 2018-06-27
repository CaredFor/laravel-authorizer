<?php

namespace Benwilkins\Authorizer\Tests;


use App\User;
use Benwilkins\Authorizer\Models\Permission;
use Benwilkins\Authorizer\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HasRolesTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testCreatesModelEvents()
    {
        $user = factory(\App\User::class)->create();
        $events = $user->getObservableEvents();

        $this->assertContains('roleGranted', $events);
        $this->assertContains('roleRevoked', $events);
    }

    public function testFiresRoleGrantedEvent()
    {
        $fired = false;
        User::roleGranted(function (Model $model) use (&$fired) { $fired = true; });
        $user = factory(\App\User::class)->create();

        $user->grantRole(Role::first());
        $this->assertTrue($fired);
    }

    public function testFiresRoleRevokedEvent()
    {
        $fired = false;
        User::roleRevoked(function (Model $model) use (&$fired) { $fired = true; });
        $user = factory(\App\User::class)->create();

        $user->grantRole(Role::first());
        $user->revokeRole(Role::first());

        $this->assertTrue($fired);
    }

    public function testGrantRole()
    {
        $user = factory(\App\User::class)->create();
        $user->grantRole(Role::first());

        $this->assertCount(1, $user->roles);
    }

    public function testGrantRoleIsChainable()
    {
        $user = factory(\App\User::class)->create();
        $result = $user->grantRole(Role::first());

        $this->assertInstanceOf(\App\User::class, $result);
    }

    public function testRoles()
    {
        $user = factory(\App\User::class)->create();
        $user->grantRole(Role::first());

        $this->assertNotNull($user->roles);
        $this->assertNotEmpty($user->roles);
        $this->assertCount(1, $user->roles);
    }

    public function testIsRole()
    {
        $user = factory(\App\User::class)->create();
        $team = config('authorizer.teams.model')::first();
        $user->grantRole(Role::findByHandle('role1'), $team);
        $user->grantRole('role2', 2);
        $user->grantRole('role3');

        $this->assertTrue($user->isRole('role1', $team));
        $this->assertTrue($user->isRole(Role::findByHandle('role1'), $team));
        $this->assertTrue($user->isRole('role2', 2));
        $this->assertTrue($user->isRole('role3'));
        $this->assertTrue($user->isRole('role3', $team)); // Global role applied to all teams
        $this->assertFalse($user->isRole('role1'));
        $this->assertFalse($user->isRole(Role::findByHandle('role1')));
        $this->assertFalse($user->isRole('role2'));
        $this->assertFalse($user->isRole(Role::findByHandle('role2')));
        $this->assertFalse($user->isRole('role2', $team));
        $this->assertFalse($user->isRole(Role::findByHandle('role2'), $team));
    }

    public function testScopeOfRoleForUser()
    {
        $user = factory(\App\User::class)->create();
        $user->grantRole('role1');

        $this->assertEquals(1, User::ofRole('role1')->count());
        $this->assertEquals(1, User::ofRole(Role::findByHandle('role1'))->count());
        $this->assertEquals(0, User::ofRole('role2')->count());
        $this->assertEquals(0, User::ofRole(Role::findByHandle('role2'))->count());
    }

    public function testScopeOfRoleForPermission()
    {
        $r1 = Role::findByHandle('role1');
        $r2 = Role::findByHandle('role2');

        $r1->grantPermission('permission1');
        $r1->grantPermission('permission2');
        $r2->grantPermission('permission2');

        $this->assertEquals(2, Permission::ofRole('role1')->count());
        $this->assertEquals(1, Permission::ofRole('role2')->count());
        $this->assertEquals(0, Permission::ofRole('role3')->count());
    }

    public function testRevokeRole()
    {
        $team = config('authorizer.teams.model')::first();
        $user = factory(\App\User::class)->create();
        $r1 = Role::findByHandle('role1');
        $r2 = Role::findByHandle('role2');

        $r1->grantPermission('permission1');
        $r2->grantPermission('permission1');
        $user->grantRole($r1);
        $user->grantRole($r2, $team);

        $this->assertEquals(2, $user->roles->count());

        $user->revokeRole($r1)->refresh();
        $this->assertEquals(1, $user->roles->count());
        $user->revokeRole($r2, $team)->refresh();
        $this->assertEquals(0, $user->roles->count());
    }

    /**
     * @expectedException \Benwilkins\Authorizer\Exceptions\RoleNotGranted
     */
    public function testRevokingUngrantedRoleThrowsError()
    {
        $user = factory(\App\User::class)->create();

        $user->revokeRole('role1');
    }

    /**
     * @expectedException \Benwilkins\Authorizer\Exceptions\RoleNotGranted
     */
    public function testRevokingUngratedRoleOnTeamThrowsError()
    {
        $user = factory(\App\User::class)->create();
        $team = config('authorizer.teams.model')::first();

        $user->grantRole('role1');
        $user->revokeRole('role1', $team);
    }
}
