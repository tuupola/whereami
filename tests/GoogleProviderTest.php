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

use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Strategy\MockClientStrategy;
use Http\Mock\Client as MockClient;
use PHPUnit\Framework\TestCase;
use Whereami\Provider\GoogleProvider;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class GoogleProviderTest extends TestCase
{
    public function setUp()
    {
        HttpClientDiscovery::prependStrategy(MockClientStrategy::class);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldConstruct()
    {
        $provider = new GoogleProvider("fakekey");
        $this->assertInstanceOf(GoogleProvider::class, $provider);
    }

    public function testShouldProcess()
    {
        $stream = new Stream("php://memory", "rb+");
        $stream->write('{"location": {"lat": 1.358496,"lng": 103.98983469999999},"accuracy": 22705.0}');
        $response = new Response($stream);

        $mockClient = new MockCLient;
        $mockClient->addResponse($response);
        $httpClient = (new HttpClientFactory($mockClient))->create();

        $networks = file_get_contents(__DIR__ . "/changi.json");
        $networks = json_decode($networks, true);

        $location = (new GoogleProvider("fakekey", $httpClient))->process($networks);

        $this->assertEquals(1.358496, $location["latitude"]);
        $this->assertEquals(103.98983469999999, $location["longitude"]);
        $this->assertEquals(22705.0, $location["accuracy"]);
    }
}
