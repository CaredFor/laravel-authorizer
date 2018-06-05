<?php


namespace Benwilkins\Authorizer;


use Illuminate\Support\Facades\Facade;

/**
 * Class AuthorizerFacade
 * @package Benwilkins\Authorizer
 *
 * @method static permissions()
 * @method static roles()
 * @method static getClass(string $modelName)
 * @method static flushCache(string $type = null)
 */
class AuthorizerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'authorizer';
    }
}