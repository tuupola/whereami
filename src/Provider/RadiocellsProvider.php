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

use Whereami\Exception\NotFoundException;
use Whereami\Exception\BadRequestException;
use Whereami\Provider;

final class RadiocellsProvider extends AbstractProvider implements Provider
{
    protected $endpoint = "https://radiocells.org/backend/geolocate";

    protected function transform($data = [])
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

    protected function parse($json)
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
