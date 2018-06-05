<?php

namespace Benwilkins\Authorizer\Tests;

use Benwilkins\Authorizer\Exceptions\RoleInvalid;

class RoleInvalidTest extends TestCase
{

    public function testCreate()
    {
        $e = RoleInvalid::create('badHandle');

        $this->assertEquals("Invalid Role (badHandle)", $e->getMessage());
    }
}
