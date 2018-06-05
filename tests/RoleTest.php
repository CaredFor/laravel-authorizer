<?php


namespace Benwilkins\Authorizer\Tests;


use Benwilkins\Authorizer\Models\Role;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class RoleTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testFindByHandle()
    {
        $retrievedRole = Role::findByHandle('role1');
        
        $this->assertNotNull($retrievedRole);
    }

    /**
     * @expectedException \Benwilkins\Authorizer\Exceptions\RoleInvalid
     */
    public function testInvalidHandleThrowsError()
    {
        Role::findByHandle('foo');
    }

    /**
     * @expectedException \Illuminate\Database\QueryException
     */
    public function testDuplicateHandleThrowsError()
    {
        Role::create(['handle' => 'role1', 'display_name' => 'Test Role 1']);
    }

    public function testUsers()
    {
        /** @var Role $role */
        $role = Role::first();
        $user = factory(\App\User::class)->create();

        $user->grantRole($role);
        $role->refresh();

        $this->assertTrue($role->users->first()->is($user));
        $this->assertCount(1, $role->users);
    }
}
