<?php

/*
 * This file is part of whereami package
 *
 * Copyright (c) 2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/whereami
 *
 */

namespace Whereami;

use PHPUnit\Framework\TestCase;
use Whereami\Adapter\LocateMeAdapter;

class LocateMeAdapterTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldProcessAndParse()
    {
        $expected = [
            "latitude" => 7.975332,
            "longitude" => 98.339406,
            "accuracy" => 10,
        ];
        $command = "echo '7.975332,98.339406,10'";
        $result = (new LocateMeAdapter($command))->process();
        $this->assertEquals($expected, $result);
    }

    public function testShouldSetCommand()
    {
        $command = "/bin/true --something";
        $adapter = new LocateMeAdapter($command);

        /* Closure kludge to test private properties. */
        $self = $this;
        $closure = function () use ($self) {
            $self->assertEquals("/bin/true --something", $this->command);
        };

        call_user_func($closure->bindTo($adapter, LocateMeAdapter::class));
    }
}
