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
use Whereami\Scanner\AirportScanner;

class AirportScannerTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldParse()
    {
        $output = file(__DIR__ . "/changi.txt");
        $result = (new AirportScanner())->parse($output);
        $this->assertEquals(8, count($result));
        $this->assertEquals("MTN-MobileWiFi 68", $result[0]["name"]);
        $this->assertEquals("44:6e:e5:73:28:51", $result[0]["address"]);
        $this->assertEquals(-89, $result[0]["signal"]);
        $this->assertEquals(10, $result[0]["channel"]);
    }
}
