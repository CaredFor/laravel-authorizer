<?php


namespace Benwilkins\Authorizer\Exceptions;


class ModelNameInvalid extends \Exception
{
    public static function create()
    {
        return new static();
    }
}