<?php


namespace Benwilkins\Authorizer\Models;

use App\User;
use Benwilkins\Authorizer\Traits\FlushesAuthorizerCache;
use Benwilkins\Authorizer\Traits\HasPermissions;
use Benwilkins\Authorizer\Traits\UuidForKey;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * @param $id
 * @param $handle
 * @param $display_name
 * @package Benwilkins\Authorizer\Models
 */
class RolesAssignedLog extends Model
{
    use UuidForKey, HasPermissions, FlushesAuthorizerCache;

    protected $table = 'roles_assigned_log';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'entity_id',
        'entity_type',
        'team_id',
        'facility_id',
        'role_id',
        'role_assigned_at',
        'role_removed_at'
    ];

    public function entity()
    {
        return $this->morphTo();
    }
}
