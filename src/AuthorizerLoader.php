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
        $this->gate->before(function (Authorizable $user, string $ability) {
            try {
                if (method_exists($user, 'hasPermission')) {
                    return $user->isGrantedPermission($ability) ?: null;
                }
            } catch (PermissionInvalid $exception) {
            }
        });
    }
}