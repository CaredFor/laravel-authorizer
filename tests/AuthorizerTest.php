<?php

namespace Benwilkins\Authorizer\Tests;

use Benwilkins\Authorizer\AuthorizerFacade as Authorizer;
use Benwilkins\Authorizer\Models\Permission;
use Benwilkins\Authorizer\Models\Role;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthorizerTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testPermissions()
    {
        $realCount = Permission::count();

        $this->assertEquals($realCount, Authorizer::permissions()->count());
    }

    public function testGetClass()
    {
        $this->assertEquals(Role::class, Authorizer::getClass('role'));
        $this->assertEquals(Permission::class, Authorizer::getClass('permission'));
    }

    /**
     * @expectedException \Benwilkins\Authorizer\Exceptions\ModelNameInvalid
     */
    public function testGetClassThrowsInvalidException()
    {
        Authorizer::getClass('foo');
    }

    public function testRoles()
    {
        $realCount = Role::count();

        $this->assertEquals($realCount, Authorizer::roles()->count());
    }

    public function testFlushCache()
    {
        $realCount = Role::count();
        $this->assertEquals($realCount, Authorizer::roles()->count());

        Role::create(['handle' => 'testCacheRole', 'display_name' => 'Test']);
        Authorizer::flushCache();

        $this->assertEquals($realCount+1, Authorizer::roles()->count());
    }
}
