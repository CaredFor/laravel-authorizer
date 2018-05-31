<?php


namespace Benwilkins\Authorizer\Contracts;


use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Permission
{
    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany;

    public static function findByHandle(string $handle): self;
}