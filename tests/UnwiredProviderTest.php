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
use Whereami\Provider\UnwiredProvider;

class UnwiredProviderTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldConstruct()
    {
        $provider = new UnwiredProvider("fakekey");
        $this->assertInstanceOf(UnwiredProvider::class, $provider);
    }

    public function testShouldProcess()
    {
        if (false === $key = getenv("WHEREAMI_UNWIRED_KEY")) {
            $this->markTestSkipped("Unwired API key not set.");
        }

        $location = (new UnwiredProvider($key))->process([
            [
                "name" => "CrownePlaza",
                "address" => "54:3d:37:2e:60:88",
                "signal" => -71,
                "channel" => 1,
            ],
            [
                "name" => "Boingo",
                "address" => "54:3d:37:ae:60:88",
                "signal" => -73,
                "channel" => 1,
            ],
        ]);
        $this->assertArrayHasKey("latitude", $location);
        $this->assertArrayHasKey("longitude", $location);
        $this->assertArrayHasKey("accuracy", $location);
    }
}
