<?php

namespace Benwilkins\Authorizer\Observers;

use Benwilkins\Authorizer\Models\RoleAssigned;
use Carbon\Carbon;

class RoleAssignedObserver
{
    public function deleting(RoleAssigned $roleAssigned)
    {
//      dd($roleAssigned);
//        $roleAssigned->deleted_at = Carbon::now();
//        $roleAssigned->save();
//        return false;
    }

    public function deleted(RoleAssigned $roleAssigned)
    {

       dd($roleAssigned);
    }

    public function creating(RoleAssigned $roleAssigned)
    {
//        dd($roleAssigned);
    }
}
