<?php

namespace Benwilkins\Authorizer\Tests;

use Benwilkins\Authorizer\Models\Permission;
use Benwilkins\Authorizer\Models\Role;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PermissionTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testFindByHandle()
    {
        $p = Permission::findByHandle('permission1');

        $this->assertNotNull($p);
        $this->assertEquals('permission1', $p->handle);
    }

    /**
     * @expectedException \Benwilkins\Authorizer\Exceptions\PermissionInvalid
     */
    public function testInvalidHandleThrowsError()
    {
        Permission::findByHandle('foo');
    }

    /**
     * @expectedException \Illuminate\Database\QueryException
     */
    public function testDuplicateHandleThrowsError()
    {
        Permission::create(['handle' => 'permission1', 'display_name' => 'Permission 1']);
    }

    public function testRoles()
    {
        $r1 = Role::findByHandle('role1');
        $r2 = Role::findByHandle('role2');

        $r1->grantPermission('permission1');
        $r1->grantPermission('permission2');
        $r2->grantPermission('permission2');

        $this->assertEquals(1, Permission::findByHandle('permission1')->roles->count());
        $this->assertEquals(2, Permission::findByHandle('permission2')->roles->count());
        $this->assertEquals(0, Permission::findByHandle('permission3')->roles->count());
    }
}
