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
use Whereami\Exception\BadRequestException;
use Whereami\Factory\HttpClientFactory;
use Whereami\Provider\BrowserlocationProvider;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class BrowserlocationProviderTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldConstruct()
    {
        $provider = new BrowserlocationProvider("fakekey");
        $this->assertInstanceOf(BrowserlocationProvider::class, $provider);
    }

    public function testShouldProcess()
    {
        $stream = new Stream("php://memory", "rb+");
        $stream->write('{"location": {"lat": 1.358496,"lng": 103.98983469999999},"accuracy": 40}');
        $response = new Response($stream);

        $mockClient = new MockCLient;
        $mockClient->addResponse($response);
        $httpClient = (new HttpClientFactory($mockClient))->create();

        $networks = file_get_contents(__DIR__ . "/changi.json");
        $networks = json_decode($networks, true);

        $location = (new BrowserlocationProvider("fakekey", $httpClient))->process($networks);

        $this->assertEquals(1.358496, $location["latitude"]);
        $this->assertEquals(103.98983469999999, $location["longitude"]);
        $this->assertEquals(40, $location["accuracy"]);
    }


    public function testShouldThrowNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $stream = new Stream("php://memory", "rb+");
        $stream->write('{"accuracy" : 16280, "location" : {"lat": 1.358496, ');
        $stream->write('"lng": 103.98983469999999}, "status" : "OK" }');
        $response = new Response($stream, 200);

        $mockClient = new MockCLient;
        $mockClient->addResponse($response);
        $httpClient = (new HttpClientFactory($mockClient))->create();

        $networks = file_get_contents(__DIR__ . "/changi.json");
        $networks = json_decode($networks, true);

        $location = (new BrowserlocationProvider("fakekey", $httpClient))->process($networks);
    }


    public function testShouldThrowBadRequestException()
    {
        $this->expectException(BadRequestException::class);

        $stream = new Stream("php://memory", "rb+");
        $stream->write('{"status" : "INVALID_REQUEST"}');
        $response = new Response($stream, 400);

        $mockClient = new MockCLient;
        $mockClient->addResponse($response);
        $httpClient = (new HttpClientFactory($mockClient))->create();

        $networks = file_get_contents(__DIR__ . "/changi.json");
        $networks = json_decode($networks, true);

        $location = (new BrowserlocationProvider("fakekey", $httpClient))->process($networks);
    }
}
