<?php

namespace Benwilkins\Authorizer\Tests;


use Benwilkins\Authorizer\AuthorizerFacade as Authorizer;
use Benwilkins\Authorizer\Models\Permission;
use Benwilkins\Authorizer\Models\Role;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HasPermissionsTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testGrantPermission()
    {
        // to user by team
        $team = config('authorizer.teams.model')::first();
        /** @var \App\User $user */
        $user = factory(\App\User::class)->create();

        $user->grantPermission('permission1', $team);
        $this->assertEquals(1, $user->permissions->count());
        $this->assertEquals($team->id, $user->permissions->first()->pivot->team_id);

        // to user global
        $user->grantPermission('permission2');
        $user->grantPermission(Permission::findByHandle('permission3'));
        $this->assertEquals(3, $user->refresh()->permissions->count());
        $this->assertNull($user->permissions->last()->pivot->team_id);

        // to role (always global)
        $role = Authorizer::roles()->first();
        $permissionCount = $role->permissions->count();

        $role->grantPermission('permission1');
        $this->assertEquals($permissionCount+1, $role->refresh()->permissions->count());
    }

    public function testGrantPermissionIsChainable()
    {
        $user = factory(\App\User::class)->create();
        $result = $user->grantPermission('permission1');
        $user->grantPermission('permission2')->refresh();

        $this->assertInstanceOf(\App\User::class, $result);
    }

    public function testPermissions()
    {
        // by role
        $role = Authorizer::roles()->first();
        $roleCount = $role->permissions->count();

        $role->grantPermission('permission1')->refresh();
        $this->assertEquals($roleCount+1, $role->permissions->count());

        // by user
        $user = factory(\App\User::class)->create();

        $user->grantPermission('permission1')->refresh();
        $this->assertEquals(1, $user->permissions->count());
    }

    public function testAllPermissions()
    {
        // by role
        $role = Authorizer::roles()->first();

        $role->grantPermission('permission1')->refresh();
        $this->assertEquals(1, $role->permissions->count());

        // by user
        $user = factory(\App\User::class)->create();

        $user->grantRole($role);
        $user->grantPermission('permission2');
        $this->assertEquals(2, $user->permissions->count());
        $this->assertTrue($user->permissions->contains('handle', 'permission1'));
        $this->assertTrue($user->permissions->contains('handle', 'permission2'));
    }

    public function testIsGrantedPermission()
    {
        $user = factory(\App\User::class)->create();

        // by role global
        $role = Role::findByHandle('role1');

        $role->grantPermission('permission1');
        $user->grantRole($role);

        $this->assertTrue($user->isGrantedPermission('permission1'));
        $this->assertFalse($user->isGrantedPermission('permission2'));
        $this->assertFalse($user->isGrantedPermission('permission3'));

        // by role team
        $role = Role::findByHandle('role2');
        $team = config('authorizer.teams.model')::first();

        $role->grantPermission('permission2');
        $user->grantRole($role, $team);

        $this->assertTrue($user->isGrantedPermission('permission2', $team));
        $this->assertFalse($user->isGrantedPermission('permission2'));
        $this->assertFalse($user->isGrantedPermission(Permission::findByHandle('permission3'), $team));

        // by direct global
        $user = factory(\App\User::class)->create();

        $user->grantPermission('permission1');
        $this->assertTrue($user->isGrantedPermission('permission1'));
        $this->assertFalse($user->isGrantedPermission('permission2'));

        // by direct team
        $permission = Permission::findByHandle('permission3');
        $user->grantPermission($permission, $team);
        $this->assertTrue($user->isGrantedPermission($permission, $team));
        $this->assertFalse($user->isGrantedPermission('permission2', $team));
        $this->assertFalse($user->isGrantedPermission($permission));
    }

    public function testRevokePermission()
    {
        $user = factory(\App\User::class)->create();
        $team = config('authorizer.teams.model')::first();

        $user->grantPermission('permission1');
        $user->grantPermission('permission2', $team)->refresh();

        $this->assertEquals(2, $user->permissions->count());
        $user->revokePermission('permission1');
        $this->assertEquals(1, $user->permissions()->count());
        $user->revokePermission('permission2', $team);
        $this->assertEquals(0, $user->permissions()->count());
        $this->assertFalse($user->isGrantedPermission('permission1'));
        $this->assertFalse($user->isGrantedPermission('permission2', $team));
    }

    /**
     * @expectedException \Benwilkins\Authorizer\Exceptions\PermissionNotGranted
     */
    public function testRevokingUngrantedPermissionThrowsError()
    {
        $user = factory(\App\User::class)->create();

        $user->revokePermission('permission1');
    }

    /**
     * @expectedException \Benwilkins\Authorizer\Exceptions\PermissionNotGranted
     */
    public function testRevokingUngrantedPermissionForTeamThrowsError()
    {
        $user = factory(\App\User::class)->create();
        $team = config('authorizer.teams.model')::first();

        $user->grantPermission('permission1');
        $user->revokePermission('permission1', $team);
    }
}
