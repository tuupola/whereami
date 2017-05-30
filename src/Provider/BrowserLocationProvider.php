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

use Whereami\Provider;
use Whereami\Exception\NotFoundException;

final class BrowserlocationProvider extends AbstractProvider implements Provider
{
    protected $endpoint = "https://maps.googleapis.com/maps/api/browserlocation/json";

    public function process(array $data, array $options = [])
    {
        $endpoint = $this->endpoint() . $this->transform($data);

        $request = $this->requestFactory->createRequest("GET", $endpoint);
        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 600) {
            $this->handleError($response);
        }

        return $this->parse((string) $response->getBody());
    }

    protected function transform($data = [])
    {
        $query = array_map(function ($entry) {
            return "wifi=mac:{$entry["address"]}|ssid:{$entry["name"]}|ss:{$entry["signal"]}";
        }, $data);

        return "&" . implode("&", $query);
    }

    protected function parse($json)
    {
        $data = json_decode($json, true);

        /* Location was not found and API went for ip fallback. */
        if ($data["accuracy"] > 5000) {
            throw new NotFoundException("No matches found", 404);
        }

        return [
            "latitude" => (float) $data["location"]["lat"],
            "longitude" => (float) $data["location"]["lng"],
            "accuracy" => (integer) $data["accuracy"],
        ];
    }

    protected function endpoint()
    {
        return $this->endpoint .= "?" . http_build_query([
            "browser" => "whereami",
            "sensor" => "false",
        ]);
    }
}
