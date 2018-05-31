<?php


namespace Benwilkins\Authorizer;


use Illuminate\Support\Facades\Facade;

class AuthorizerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'authorizer';
    }
}