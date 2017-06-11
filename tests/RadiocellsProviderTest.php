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
use Whereami\Provider\RadiocellsProvider;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class RadiocellsProviderTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldConstruct()
    {
        $provider = new RadiocellsProvider("fakekey");
        $this->assertInstanceOf(RadiocellsProvider::class, $provider);
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

        $location = (new RadiocellsProvider("fakekey", $httpClient))->process($networks);

        $this->assertEquals(1.358496, $location["latitude"]);
        $this->assertEquals(103.98983469999999, $location["longitude"]);
        $this->assertEquals(22705, $location["accuracy"]);
    }

    public function testShouldHaveApplicationJsonHeaders()
    {
        $mockClient = new MockCLient;
        $httpClient = (new HttpClientFactory($mockClient))->create();

        $networks = file_get_contents(__DIR__ . "/changi.json");
        $networks = json_decode($networks, true);

        $location = (new RadiocellsProvider("fakekey", $httpClient))->process($networks);

        $request = $mockClient->getRequests()[0];

        $this->assertEquals(
            "application/json",
            $request->getHeaderLine("Accept")
        );
        $this->assertEquals(
            "application/json; charset=utf-8",
            $request->getHeaderLine("Content-Type")
        );
    }

    public function testShouldThrowNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $stream = new Stream("php://memory", "rb+");
        $stream->write('{"resultType": "error", "results": {"source": "none", "measurements": 0, ');
        $stream->write('"location": { "lat": 0.0, "lng": 0.0 }, "accuracy": 9999 },');
        $stream->write('"error": { "message": "Not found", "code": 404, "errors": [{');
        $stream->write('"message": "Not found", "reason": "notFound", "domain": "geolocation" }]}}');
        $response = new Response($stream, 200);

        $mockClient = new MockCLient;
        $mockClient->addResponse($response);
        $httpClient = (new HttpClientFactory($mockClient))->create();

        $networks = file_get_contents(__DIR__ . "/changi.json");
        $networks = json_decode($networks, true);

        $location = (new RadiocellsProvider("fakekey", $httpClient))->process($networks);

        $this->assertEquals(1.358496, $location["latitude"]);
        $this->assertEquals(103.98983469999999, $location["longitude"]);
        $this->assertEquals(22705, $location["accuracy"]);
    }


    public function testShouldThrowBadRequestException()
    {
        $this->expectException(BadRequestException::class);

        $stream = new Stream("php://memory", "rb+");
        $stream->write('{"resultType": "error","results": {"source": "none","measurements": 0,');
        $stream->write('"location": {"lat": 0.0,"lng": 0.0},"accuracy": 9999},"error": {');
        $stream->write('"message": "Empty request","code": 400,"errors": [{"message": null,');
        $stream->write('"reason": "parseError","domain": "global"}]}}');
        $response = new Response($stream, 200);

        $mockClient = new MockCLient;
        $mockClient->addResponse($response);
        $httpClient = (new HttpClientFactory($mockClient))->create();

        $networks = file_get_contents(__DIR__ . "/changi.json");
        $networks = json_decode($networks, true);

        $location = (new RadiocellsProvider("fakekey", $httpClient))->process($networks);

        $this->assertEquals(1.358496, $location["latitude"]);
        $this->assertEquals(103.98983469999999, $location["longitude"]);
        $this->assertEquals(22705, $location["accuracy"]);
    }
}
