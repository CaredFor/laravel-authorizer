<?php

namespace Benwilkins\Authorizer\Observers;

use App\Role;

class RoleObserver
{
    public function deleting(Role $role)
    {
        dd($role);
    }
}