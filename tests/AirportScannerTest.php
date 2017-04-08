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

    public function testShouldScan()
    {
        $data = (new AirportScanner())->scan();
        print_r($data);
    }
}
