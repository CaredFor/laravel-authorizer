<?php

namespace Benwilkins\Authorizer;

use Benwilkins\Authorizer\Contracts\Permission as PermissionContract;
use Benwilkins\Authorizer\Contracts\Role as RoleContract;
use Benwilkins\Authorizer\Models\Permission;
use Benwilkins\Authorizer\Models\Role;
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

        $loader->register();
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
}