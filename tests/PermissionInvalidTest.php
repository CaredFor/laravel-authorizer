<?php

namespace Benwilkins\Authorizer\Tests;

use Benwilkins\Authorizer\Exceptions\PermissionInvalid;

class PermissionInvalidTest extends TestCase
{

    public function testCreate()
    {
        $e = PermissionInvalid::create('badHandle', 'badGuard');

        $this->assertEquals("Invalid Permission (badHandle, badGuard)", $e->getMessage());
    }
}
