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

final class UnwiredProvider extends AbstractProvider implements Provider
{
    protected $endpoint = "https://eu1.unwiredlabs.com/v2/process.php";

    protected function transform($data = [])
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

    protected function parse($json)
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
