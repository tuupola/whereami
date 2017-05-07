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
use Whereami\Provider\CombainProvider;
use Whereami\Exception\NotFoundException;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class CombainProviderTest extends TestCase
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
        $provider = new CombainProvider("fakekey");
        $this->assertInstanceOf(CombainProvider::class, $provider);
    }

    public function testShouldProcess()
    {
        $stream = new Stream("php://memory", "rb+");
        $stream->write('{"location":{"lat":1.35985,"lng":103.9608},"accuracy":24}');
        $response = new Response($stream);

        $mockClient = new MockCLient;
        $mockClient->addResponse($response);
        $httpClient = (new HttpClientFactory($mockClient))->create();

        $networks = file_get_contents(__DIR__ . "/changi.json");
        $networks = json_decode($networks, true);

        $location = (new CombainProvider("fakekey", $httpClient))->process($networks);

        $this->assertEquals(1.35985, $location["latitude"]);
        $this->assertEquals(103.9608, $location["longitude"]);
        $this->assertEquals(24, $location["accuracy"]);
    }

    public function testShouldThrowNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $stream = new Stream("php://memory", "rb+");
        $stream->write('{"error": {"errors": {"domain":"geolocation","reason":"notFound",');
        $stream->write('"message":"Not Found"},"code":404,"message":"Not Found"}}');
        $response = new Response($stream, 404);

        $mockClient = new MockCLient;
        $mockClient->addResponse($response);
        $httpClient = (new HttpClientFactory($mockClient))->create();

        $networks = file_get_contents(__DIR__ . "/changi.json");
        $networks = json_decode($networks, true);

        $location = (new CombainProvider("fakekey", $httpClient))->process($networks);

        $this->assertEquals(1.35985, $location["latitude"]);
        $this->assertEquals(103.9608, $location["longitude"]);
        $this->assertEquals(24, $location["accuracy"]);
    }
}
