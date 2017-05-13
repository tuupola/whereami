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

use Tuupola\CoreLocation\Request;
use Tuupola\CoreLocation\Response;
use Whereami\Provider;

final class AppleProvider extends AbstractProvider implements Provider
{
    protected $endpoint = "https://gs-loc.apple.com/clls/wloc";

    protected function transform($data = [])
    {
        $routers = array_map(function ($entry) {
            return $entry["address"];
        }, $data);

        return (new Request($routers))->body();
    }

    protected function parse($data)
    {
        $response = (new Response)->fromString($data);

        foreach ($response as $router) {
            /* Ignore unknown mac addresses. */
            if (-180.0 === $router["latitude"]) {
                continue;
            }
            /* Return first matching location without any calculations. */
            return [
                "latitude" => (float) $router["latitude"],
                "longitude" => (float) $router["longitude"],
                "accuracy" => (integer) $router["accuracy"],
            ];
        }
    }
}
