<?php

namespace Benwilkins\Authorizer\Models;

use App\Observers\TeamUserPivotObserver;
use Benwilkins\Authorizer\Observers\RoleAssignedObserver;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleAssigned extends MorphPivot
{
    protected $table = 'roles_assigned';
    public $incrementing = 'false';
    protected $fillable = [
        'role_id',
        'entity_id',
        'entity_type',
        'team_id',
        'facility_id'
    ];
}