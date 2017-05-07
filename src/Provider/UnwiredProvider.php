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
use RuntimeException;
use Whereami\Exception\NotFoundException;
use Whereami\HttpClientFactory;
use Whereami\Provider;

final class UnwiredProvider implements Provider
{
    private $endpoint = "https://eu1.unwiredlabs.com/v2/process.php";
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
        $json["fallbacks"] = ["ipf" => 0];
        $json["token"] = $this->apikey;
        $json["address"] = 0;
        $json["wifi"] = array_map(function ($entry) {
            return [
                "bssid" => $entry["address"],
                "signal" => $entry["signal"],
                "channel" => $entry["channel"],
            ];
        }, $data);

        return json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    private function parse($json)
    {
        $data = json_decode($json, true);

        if ("error" === $data["status"]) {
            throw new NotFoundException($data["message"], 404);
        }

        return [
            "latitude" => $data["lat"],
            "longitude" => $data["lon"],
            "accuracy" => $data["accuracy"],
        ];
    }
}
