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
use Whereami\Scanner\IwListScanner;

class IwListScannerTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldParse()
    {
        $output = file(__DIR__ . "/iwlist.txt");
        $result = (new IwListScanner())->parse($output);
        $this->assertEquals(17, count($result));
        $this->assertEquals("TRENDnet 712", $result[5]["name"]);
        $this->assertEquals("D8:EB:97:17:EB:8F", $result[5]["address"]);
        $this->assertEquals(-74, $result[5]["signal"]);
        $this->assertEquals(6, $result[5]["channel"]);
    }
}
