<?php


namespace Benwilkins\Authorizer\Exceptions;


class PermissionNotGranted extends \Exception
{
    public static function create(...$params)
    {
        $paramString = implode(', ', $params);
        return new static("Permission not granted to user ({$paramString})");
    }
}