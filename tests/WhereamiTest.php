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
use Whereami\Adapter\CoreLocationAdapter;
use Whereami\Factory\HttpClientFactory;
use Whereami\Scanner\AirportScanner;
use Whereami\Provider\EmulatedProvider;
use Whereami\Provider\MozillaProvider;
use Whereami\Whereami;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class WhereamiTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldConstructWithProvider()
    {
        $command = "echo '7.975332,98.339406,10'";
        $adapter = new CoreLocationAdapter($command);
        $provider = new EmulatedProvider($adapter);

        $locator = new Whereami($provider);
        $this->assertInstanceOf(Whereami::class, $locator);
    }

    public function testShouldConstructWithAdapter()
    {
        $command = "echo '7.975332,98.339406,10'";
        $adapter = new CoreLocationAdapter($command);

        $locator = new Whereami($adapter);
        $this->assertInstanceOf(Whereami::class, $locator);
    }

    public function testShouldThrowFromConstruct()
    {
        $this->expectException(\RuntimeException::class);
        $locator = new Whereami(null);
    }

    public function testShouldFindCurrentLocation()
    {
        $stream = new Stream("php://memory", "rb+");
        $stream->write('{"location": {"lat": 1.3578561, "lng": 103.9885244}, "accuracy": 55.6020161}');
        $response = new Response($stream);

        $mockClient = new MockCLient;
        $mockClient->addResponse($response);
        $httpClient = (new HttpClientFactory($mockClient))->create();

        $command = "/bin/cat " . __DIR__ . "/changi.txt";
        $scanner = new AirportScanner($command);

        $provider =  new MozillaProvider("fakekey", $httpClient);
        $locator = new Whereami($provider, $scanner);
        $location = $locator->whereami();

        $this->assertEquals(1.3578561, $location["latitude"]);
        $this->assertEquals(103.9885244, $location["longitude"]);
        $this->assertEquals(55, $location["accuracy"]);

        $this->assertInstanceOf(Whereami::class, $locator);
    }

    public function testShouldFindEmulatedCurrentLocation()
    {
        $command = "echo '7.975332,98.339406,10'";
        $adapter = new CoreLocationAdapter($command);
        $provider = new EmulatedProvider($adapter);

        $locator = new Whereami($provider);
        $location = $locator->whereami();

        $this->assertEquals(7.975332, $location["latitude"]);
        $this->assertEquals(98.339406, $location["longitude"]);
        $this->assertEquals(10, $location["accuracy"]);

        $this->assertInstanceOf(Whereami::class, $locator);
    }

    public function testShouldFindThirdPartyLocation()
    {
        $stream = new Stream("php://memory", "rb+");
        $stream->write('{"location": {"lat": 1.3578561, "lng": 103.9885244}, "accuracy": 55.6020161}');
        $response = new Response($stream);

        $mockClient = new MockCLient;
        $mockClient->addResponse($response);
        $httpClient = (new HttpClientFactory($mockClient))->create();

        $provider =  new MozillaProvider("fakekey", $httpClient);
        $locator = new Whereami($provider);

        $networks = file_get_contents(__DIR__ . "/changi.json");
        $networks = json_decode($networks, true);

        $location = $locator->whereis($networks);

        $this->assertEquals(1.3578561, $location["latitude"]);
        $this->assertEquals(103.9885244, $location["longitude"]);
        $this->assertEquals(55, $location["accuracy"]);

        $this->assertInstanceOf(Whereami::class, $locator);
    }
}
