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

namespace Whereami\Scanner;

use Whereami\Scanner;

final class AirportScanner implements Scanner
{
    private $command;

    public function __construct(
        $command =
        "/System/Library/PrivateFrameworks/Apple80211.framework/Versions" .
        "/Current/Resources/airport --scan 2>&1"
    ) {
        $this->command = $command;
    }

    public function scan()
    {
        exec($this->command, $output);
        return $this->parse($output);
    }

    private function parse(array $output)
    {
        array_shift($output);
        return array_map(function ($line) {
            $data = preg_split("/\s+/", $line);
            /* Airport sometimes return 157,+1 style channels. */
            preg_match("/^(\d+)/", $data[4], $matches);
            $channel = $matches[0];

            return [
                "name" => $data[1], // SSID
                "address" => $data[2], // BSSID
                "signal" => $data[3], // RSSI
                "channel" => $channel,
            ];
        }, $output);
    }
}
