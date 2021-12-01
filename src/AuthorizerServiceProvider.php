<?php

namespace Benwilkins\Authorizer;

use Benwilkins\Authorizer\Commands\AssignPermissionToRole;
use Benwilkins\Authorizer\Commands\CreatePermission;
use Benwilkins\Authorizer\Commands\CreateRole;
use Benwilkins\Authorizer\Contracts\Permission as PermissionContract;
use Benwilkins\Authorizer\Contracts\Role as RoleContract;
use Benwilkins\Authorizer\Models\Permission;
use Benwilkins\Authorizer\Models\Role;
use Benwilkins\Authorizer\Models\RoleAssigned;
use Benwilkins\Authorizer\Observers\RoleAssignedObserver;
use App\User;
use Illuminate\Support\ServiceProvider;

class AuthorizerServiceProvider extends ServiceProvider
{
    public function boot(AuthorizerLoader $loader)
    {
        $this->publishes([
            __DIR__ . '/../config/authorizer.php' => config_path('authorizer.php'),
        ], 'config');

        if (! class_exists('CreateAuthorizerTables')) {
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__ . '/../database/migrations/create_authorizer_tables.php.stub' => $this->app->databasePath()."/migrations/{$timestamp}_create_authorizer_tables.php",
            ], 'migrations');
        }

        if (! class_exists('AddFacilityIdToAuthorizerTables')) {
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__ . '/../database/migrations/add_facility_id_to_authorizer_tables.php' => $this->app->databasePath()."/migrations/{$timestamp}_add_facility_id_to_authorizer_tables.php",
            ], 'migrations');
        }

        RoleAssigned::observe(RoleAssignedObserver::class);

        $loader->register();
        $this->registerCommands();
    }

    public function register()
    {
        $this->registerFacades();
        $this->registerContracts();
    }

    protected function registerFacades()
    {
        $this->app->alias(Authorizer::class, 'authorizer');
    }

    protected function registerContracts()
    {
        $this->app->bind(PermissionContract::class, Permission::class);
        $this->app->bind(RoleContract::class, Role::class);
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AssignPermissionToRole::class,
                CreatePermission::class,
                CreateRole::class
            ]);
        }
    }
}
