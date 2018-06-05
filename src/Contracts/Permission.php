<?php


namespace Benwilkins\Authorizer\Contracts;


use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Permission
{
    public function roles();
    public static function findByHandle(string $handle): self;
}