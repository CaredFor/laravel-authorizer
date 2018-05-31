<?php


namespace Benwilkins\Authorizer;


use Benwilkins\Authorizer\Contracts\Permission;
use Benwilkins\Authorizer\Contracts\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class Authorizer
{
    const CACHE_KEY_PREFIX = 'authorizer.';
    const CACHE_PERMISSIONS_KEY = 'permissions';
    const CACHE_ROLES_KEY = 'roles';

    public function __construct()
    {
    }

    /**
     * Gets all available permissions.
     *
     * @return Collection
     */
    public function permissions(): Collection
    {
        return Cache::remember(
            static::CACHE_KEY_PREFIX.static::CACHE_PERMISSIONS_KEY,
            config('authorizer.cache_expiration'),
            function () {
                app(Permission::class)->with('roles')->get();
            }
        );
    }

    /**
     * Gets all available roles.
     *
     * @return Collection
     */
    public function roles(): Collection
    {
        return Cache::remember(
            static::CACHE_KEY_PREFIX.static::CACHE_ROLES_KEY,
            config('authorizer.cache_expiration'),
            function () {
                app(Role::class)->with('permissions')->get();
            }
        );
    }
}