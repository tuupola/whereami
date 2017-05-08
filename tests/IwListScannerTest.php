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

    public function testShouldScanAndParse()
    {
        $command = "/bin/cat " . __DIR__ . "/iwlist.txt";
        $result = (new IwListScanner($command))->scan();
        $this->assertEquals(17, count($result));
        $this->assertEquals("TRENDnet 712", $result[5]["name"]);
        $this->assertEquals("D8:EB:97:17:EB:8F", $result[5]["address"]);
        $this->assertEquals(-74, $result[5]["signal"]);
        $this->assertEquals(6, $result[5]["channel"]);
    }

    public function testShouldSetCommand()
    {
        $command = "/bin/true --something";
        $scanner = new IwListScanner($command);

        /* Closure kludge to test private properties. */
        $self = $this;
        $closure = function () use ($self) {
            $self->assertEquals("/bin/true --something", $this->command);
        };

        call_user_func($closure->bindTo($scanner, IwListScanner::class));
    }
}
