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

    const REGEXP_SSID = "(?<ssid>.*)";
    const REGEXP_BSSID = "\s+(?<bssid>([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2}))";
    const REGEXP_SIGNAL = "\s+(?<signal>-\d+)";
    const REGEXP_CHANNEL = "\s+(?<channel>\d+)";
    const BINARY = "/System/Library/PrivateFrameworks/Apple80211.framework/Versions//Current/Resources/airport";

    public function __construct($command = self::BINARY . " --scan 2>&1")
    {
        $this->command = $command;
    }

    public function scan()
    {
        exec($this->command, $output);
        return $this->parse($output);
    }

    private function parse(array $output)
    {
        $output = array_map(function ($line) {
            $regexp = self::REGEXP_SSID
                    . self::REGEXP_BSSID
                    . self::REGEXP_SIGNAL
                    . self::REGEXP_CHANNEL;

            preg_match("/{$regexp}/", $line, $matches);

            if (count($matches)) {
                return [
                    "name" => trim($matches["ssid"]),
                    "address" => $matches["bssid"],
                    "signal" => $matches["signal"],
                    "channel" => $matches["channel"],
                ];
            }
        }, $output);

        return array_values(array_filter($output));
    }
}
