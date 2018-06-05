<?php

namespace Benwilkins\Authorizer\Tests;

use Benwilkins\Authorizer\Models\Permission;
use Benwilkins\Authorizer\Models\Role;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function setUpDatabase()
    {
        $this->seedRoles();
        $this->seedPermissions();
        $this->seedTeams();
    }

    protected function seedRoles()
    {
        Role::create([
            'handle' => 'role1',
            'display_name' => 'Test Role 1'
        ]);
        Role::create([
            'handle' => 'role2',
            'display_name' => 'Test Role 2'
        ]);
        Role::create([
            'handle' => 'role3',
            'display_name' => 'Test Role 3'
        ]);
    }

    protected function seedPermissions()
    {
        Permission::create([
            'handle' => 'permission1',
            'display_name' => 'Test Permission 1',
            'guard' => 'api'
        ]);
        Permission::create([
            'handle' => 'permission2',
            'display_name' => 'Test Permission 2',
            'guard' => 'api'
        ]);
        Permission::create([
            'handle' => 'permission3',
            'display_name' => 'Test Permission 3',
            'guard' => 'api'
        ]);
    }

    protected function seedTeams()
    {
        \App\Team::create(['name' => 'Team 1']);
        \App\Team::create(['name' => 'Team 2']);
    }
}
