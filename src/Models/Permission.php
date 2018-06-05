<?php


namespace Benwilkins\Authorizer\Models;

use Benwilkins\Authorizer\Contracts\Permission as PermissionContract;
use Benwilkins\Authorizer\AuthorizerFacade as Authorizer;
use Benwilkins\Authorizer\Exceptions\PermissionInvalid;
use Benwilkins\Authorizer\Traits\FlushesAuthorizerCache;
use Benwilkins\Authorizer\Traits\HasRoles;
use Benwilkins\Authorizer\Traits\UuidForKey;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 * @package Benwilkins\Authorizer\Models
 * @param $id
 * @param $handle
 * @param $display_name
 * @param $guard
 */
class Permission extends Model implements PermissionContract
{
    use UuidForKey, HasRoles, FlushesAuthorizerCache;

    protected $fillable = [
        'handle',
        'display_name',
        'guard'
    ];

    public function roles()
    {
        return $this->morphedByMany(Role::class, 'entity', config('authorizer.tables.permissions_assigned'));
    }

    public static function findByHandle(string $handle, string $guard = null): PermissionContract
    {
        $guard = $guard ?: config('authorizer.default_guard');
        $permission = Authorizer::permissions()->filter(function ($permission) use ($handle, $guard) {
            return $permission->handle === $handle && $permission->guard === $guard;
        })->first();

        if (! $permission) {
            throw PermissionInvalid::create($handle, $guard);
        }

        return $permission;
    }
}