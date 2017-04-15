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
use Whereami\Adapter\CoreLocationAdapter;

class CoreLocationAdapterTest extends TestCase
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
        $result = (new CoreLocationAdapter($command))->process();
        $this->assertEquals($expected, $result);
    }
}
