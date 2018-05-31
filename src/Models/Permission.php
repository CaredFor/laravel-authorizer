<?php


namespace Benwilkins\Authorizer\Models;

use Benwilkins\Authorizer\Contracts\Permission as PermissionContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission implements PermissionContract
{
    public function roles(): BelongsToMany
    {
        // TODO: Implement roles() method.
    }

    public static function findByHandle(string $handle): PermissionContract
    {
        // TODO: Implement findByHandle() method.
    }
}