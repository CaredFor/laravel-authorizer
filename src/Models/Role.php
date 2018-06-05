<?php


namespace Benwilkins\Authorizer\Models;

use App\User;
use Benwilkins\Authorizer\AuthorizerFacade as Authorizer;
use Benwilkins\Authorizer\Contracts\Role as RoleContract;
use Benwilkins\Authorizer\Exceptions\RoleInvalid;
use Benwilkins\Authorizer\Traits\FlushesAuthorizerCache;
use Benwilkins\Authorizer\Traits\HasPermissions;
use Benwilkins\Authorizer\Traits\UuidForKey;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * @package Benwilkins\Authorizer\Models
 * @param $id
 * @param $handle
 * @param $display_name
 */
class Role extends Model implements RoleContract
{
    use UuidForKey, HasPermissions, FlushesAuthorizerCache;

    protected $fillable = [
        'handle',
        'display_name'
    ];

    public function users()
    {
        return $this->morphedByMany(
            User::class,
            'entity',
            config('authorizer.tables.roles_assigned')
        );
    }

    public static function findByHandle(string $handle): RoleContract
    {
        $role = Authorizer::roles()->filter(function ($role) use ($handle) {
            return $role->handle === $handle;
        })->first();

        if (! $role) {
            throw RoleInvalid::create($handle);
        }

        return $role;
    }
}