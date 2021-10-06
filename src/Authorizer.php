<?php


namespace Benwilkins\Authorizer;


use Benwilkins\Authorizer\Contracts\Permission;
use Benwilkins\Authorizer\Contracts\Role;
use Benwilkins\Authorizer\Exceptions\ModelNameInvalid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Class Authorizer
 * @package Benwilkins\Authorizer
 */
class Authorizer
{
    const CACHE_KEY_PREFIX = 'authorizer.';
    const CACHE_PERMISSIONS_KEY = 'permissions';
    const CACHE_ROLES_KEY = 'roles';

    protected $permissionClass;
    protected $roleClass;

    public function __construct(Permission $permission, Role $role)
    {
        $this->permissionClass = get_class($permission);
        $this->roleClass = get_class($role);
    }

    /**
     * Gets all available permissions.
     *
     * @param $cacheKey
     * @return Collection
     */
    public function permissions($cacheKey = null): Collection
    {
        return app(Permission::class)->with('roles')->get();
    }

    /**
     * Gets all available roles.
     *
     * @param null $cacheKey
     * @return Collection
     */
    public function roles($cacheKey = null): Collection
    {
        return app(Role::class)->with('permissions')->get();
    }

    public function getClass(string $modelName)
    {
        $prop = $modelName.'Class';

        if (!property_exists($this, $prop)) {
            throw ModelNameInvalid::create();
        }

        return $this->$prop;
    }

    public function flushCache($type = null)
    {
        if ($type) {
            Cache::forget(static::CACHE_KEY_PREFIX.$type);
        } else {
            Cache::forget(static::CACHE_KEY_PREFIX.static::CACHE_PERMISSIONS_KEY);
            Cache::forget(static::CACHE_KEY_PREFIX.static::CACHE_ROLES_KEY);
        }
    }
}
