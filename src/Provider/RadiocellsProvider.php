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

namespace Whereami\Provider;

use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;
use Whereami\Exception\BadRequestException;
use Whereami\Exception\NotFoundException;
use Whereami\HttpClientFactory;
use Whereami\Provider;

final class RadiocellsProvider implements Provider
{
    private $endpoint = "https://radiocells.org/backend/geolocate";

    private $apikey;
    private $httpClient;
    private $requestFactory;

    public function __construct(
        $apikey,
        HttpClient $httpClient = null,
        RequestFactory $requestFactory = null
    ) {
        $this->apikey = $apikey;
        $this->httpClient = $httpClient ?: (new HttpClientFactory)->create();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
    }

    public function process(array $data, array $options = [])
    {
        $endpoint = $this->endpoint();
        $headers = [];
        $body = $this->transform($data);
        $request = $this->requestFactory->createRequest("POST", $endpoint, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        return $this->parse((string) $response->getBody());
    }

    private function endpoint()
    {
        return $this->endpoint;
    }

    private function transform($data = [])
    {
        $json["considerIp"] = false;
        $json["wifiAccessPoints"] = array_map(function ($entry) {
            return [
                "ssid" => $entry["name"],
                "macAddress" => $entry["address"],
                "signalStrength" => $entry["signal"],
                "channel" => $entry["channel"],
            ];
        }, $data);

        return json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    private function parse($json)
    {
        $data = json_decode($json, true);

        /* Radiocells returns 200 OK even in case of error. */
        if (isset($data["resultType"]) && "error" === $data["resultType"]) {
            if (404 === $data["error"]["code"]) {
                throw new NotFoundException($data["error"]["message"], 404);
            }
            throw new BadRequestException($data["error"]["message"], $data["error"]["code"]);
        }

        return [
            "latitude" => (float) $data["location"]["lat"],
            "longitude" => (float) $data["location"]["lng"],
            "accuracy" => (integer) $data["accuracy"],
        ];
    }
}
