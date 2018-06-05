<?php


namespace Benwilkins\Authorizer\Contracts;


use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Interface Role
 * @package Benwilkins\Authorizer\Contracts
 * @property string $id
 * @property string $handle
 * @property string $display_name
 */
interface Role
{
    public function permissions(): MorphToMany;

    public function users();

    public static function findByHandle(string $handle): self;
}