<?php


namespace Benwilkins\Authorizer\Exceptions;


class RoleInvalid extends \Exception
{
    public static function create(...$params)
    {
        $paramString = implode(', ', $params);

        return new static("Invalid Role ({$paramString})");
    }

}