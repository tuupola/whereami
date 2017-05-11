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

use Http\Mock\Client as MockClient;
use PHPUnit\Framework\TestCase;
use Whereami\Exception\NotFoundException;
use Whereami\Factory\HttpClientFactory;
use Whereami\Provider\MozillaProvider;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class MozillaProviderTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldConstruct()
    {
        $provider = new MozillaProvider("fakekey");
        $this->assertInstanceOf(MozillaProvider::class, $provider);
    }

    public function testShouldProcess()
    {
        $stream = new Stream("php://memory", "rb+");
        $stream->write('{"location": {"lat": 1.3578561, "lng": 103.9885244}, "accuracy": 55.6020161}');
        $response = new Response($stream);

        $mockClient = new MockCLient;
        $mockClient->addResponse($response);
        $httpClient = (new HttpClientFactory($mockClient))->create();

        $networks = file_get_contents(__DIR__ . "/changi.json");
        $networks = json_decode($networks, true);

        $location = (new MozillaProvider("fakekey", $httpClient))->process($networks);

        $this->assertEquals(1.3578561, $location["latitude"]);
        $this->assertEquals(103.9885244, $location["longitude"]);
        $this->assertEquals(55, $location["accuracy"]);
    }

    public function testShouldThrowNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $stream = new Stream("php://memory", "rb+");
        $stream->write('{"error":{"code":404,"message":"Not found","errors":[{"domain":');
        $stream->write('"geolocation","message":"Not found","reason":"notFound"}]}}');
        $response = new Response($stream, 404);

        $mockClient = new MockCLient;
        $mockClient->addResponse($response);
        $httpClient = (new HttpClientFactory($mockClient))->create();

        $networks = file_get_contents(__DIR__ . "/changi.json");
        $networks = json_decode($networks, true);

        $location = (new MozillaProvider("fakekey", $httpClient))->process($networks);

        $this->assertEquals(1.3578561, $location["latitude"]);
        $this->assertEquals(103.9885244, $location["longitude"]);
        $this->assertEquals(55, $location["accuracy"]);
    }
}
