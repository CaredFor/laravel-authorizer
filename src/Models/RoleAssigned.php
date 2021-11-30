<?php

namespace Benwilkins\Authorizer\Models;

use Benwilkins\Authorizer\Observers\RoleAssignedObserver;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleAssigned extends MorphPivot
{
    use SoftDeletes;

    protected $table = 'roles_assigned';
    public $incrementing = 'false';
    protected $fillable = [
        'role_id',
        'entity_id',
        'entity_type',
        'team_id',
        'facility_id'
    ];

//    public function delete()
//    {
////        app('events')->listen('eloquent.deleting: ' . __CLASS__, $this);
//        event('eloquent.deleting: ' . __CLASS__, $this);
//        parent::delete();
//        event('eloquent.deleted: ' . __CLASS__, $this);
//    }
}