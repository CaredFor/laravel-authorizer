<?php


namespace Benwilkins\Authorizer;


use Benwilkins\Authorizer\Exceptions\PermissionInvalid;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Gate;

class AuthorizerLoader
{
    protected $gate;

    public function __construct(Gate $gate)
    {
        $this->gate = $gate;
    }

    public function register()
    {
        $this->gate->before(function (Authorizable $user, string $ability, $params = null) {
            try {
                $team = (count($params) > 0) ? $params[0] : null;
                $facilityId = (count($params) > 1) ? $params[1] : null;

                if (method_exists($user, 'isGrantedPermission')) {
                    return $user->isGrantedPermission($ability, $team, $facilityId) ?: null;
                }
            } catch (PermissionInvalid $exception) {
            }
        });

        return true;
    }
}
