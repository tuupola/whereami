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
use Whereami\Provider\EmulatedProvider;
use Whereami\Adapter\CoreLocationAdapter;

class EmulatedProviderTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldConstruct()
    {
        $adapter = new CoreLocationAdapter;
        $provider = new EmulatedProvider($adapter);
        $this->assertInstanceOf(EmulatedProvider::class, $provider);
    }

    public function testShouldProcess()
    {
        $command = "echo '7.975332,98.339406,10'";
        $adapter = new CoreLocationAdapter($command);
        $location = (new EmulatedProvider($adapter))->process([]);

        $this->assertEquals(7.975332, $location["latitude"]);
        $this->assertEquals(98.339406, $location["longitude"]);
        $this->assertEquals(10, $location["accuracy"]);
    }

    public function testShouldTriggerWarning()
    {
        $this->expectException("PHPUnit_Framework_Error_Warning");

        $command = "echo '7.975332,98.339406,10'";
        $adapter = new CoreLocationAdapter($command);

        $networks = file_get_contents(__DIR__ . "/changi.json");
        $networks = json_decode($networks, true);

        $location = (new EmulatedProvider($adapter))->process($networks);

        $this->assertEquals(7.975332, $location["latitude"]);
        $this->assertEquals(98.339406, $location["longitude"]);
        $this->assertEquals(10, $location["accuracy"]);
    }
}
