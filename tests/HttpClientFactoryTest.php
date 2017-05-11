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

use Whereami\Factory\HttpClientFactory;

use PHPUnit\Framework\TestCase;
use Whereami\Scanner\AirportScanner;
use Http\Client\Common\PluginClient;

class HttpClientFactoryTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldCreate()
    {
        $client = (new HttpClientFactory())->create();
        $this->assertInstanceOf(PluginClient::class, $client);
    }
}
