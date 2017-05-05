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
use Whereami\Provider\UnwiredProvider;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class UnwiredProviderTest extends TestCase
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
        $provider = new UnwiredProvider("fakekey");
        $this->assertInstanceOf(UnwiredProvider::class, $provider);
    }

    public function testShouldProcess()
    {
        $stream = new Stream("php://memory", "rb+");
        $stream->write('{"status":"ok","balance":11,"lat":1.35849578,"lon":103.9881204,"accuracy":29}');
        $response = new Response($stream);

        $mockClient = new MockCLient;
        $mockClient->addResponse($response);
        $httpClient = (new HttpClientFactory($mockClient))->create();

        $networks = file_get_contents(__DIR__ . "/changi.json");
        $networks = json_decode($networks, true);

        $location = (new UnwiredProvider("fakekey", null, $httpClient))->process($networks);

        $this->assertEquals(1.35849578, $location["latitude"]);
        $this->assertEquals(103.9881204, $location["longitude"]);
        $this->assertEquals(29, $location["accuracy"]);
    }
}
