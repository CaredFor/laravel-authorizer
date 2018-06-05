<?php


namespace Benwilkins\Authorizer\Traits;


use \Benwilkins\Authorizer\AuthorizerFacade as Authorizer;

trait FlushesAuthorizerCache
{
    public static function bootFlushesAuthorizerCache()
    {
        static::saved(function () {
            Authorizer::flushCache();
        });

        static::deleted(function () {
            Authorizer::flushCache();
        });
    }
}