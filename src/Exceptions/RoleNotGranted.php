<?php


namespace Benwilkins\Authorizer\Exceptions;


class RoleNotGranted extends \Exception
{
    public static function create(...$params)
    {
        $paramString = implode(', ', $params);
        return new static("Role not granted to user ({$paramString})");
    }
}