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
        array $options = null,
        HttpClient $httpClient = null,
        RequestFactory $requestFactory = null
    ) {
        $this->apikey = $apikey;
        $this->options = $options;
        $this->httpClient = $httpClient ?: (new HttpClientFactory)->create();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
    }

    public function process(array $data = [])
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

    private function transform($data)
    {
        /* Anything truthy means enable ip fallback. */
        if (!empty($this->options["ip"])) {
            $json["fallbacks"] = ["ipf" => 1];
        }

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
        return [
            "latitude" => $data["lat"],
            "longitude" => $data["lon"],
            "accuracy" => $data["accuracy"],
        ];
    }
}
