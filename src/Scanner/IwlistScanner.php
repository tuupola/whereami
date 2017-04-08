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

final class IwListScanner implements Scanner
{
    const REGEXP_ESSID = '/ESSID:"([^"]+)"/';
    const REGEXP_BSSID = "/Address: ([:0-9a-fA-F]+)/";
    const REGEXP_SIGNAL = "/Signal level[:=](-?[0-9]+) dBm/";
    const REGEXP_CHANNEL = "/Channel:([0-9]+)/";

    private $command;

    public function __construct($command = "/sbin/iwlist 2>&1")
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
        $current = -1;
        $networks = [];
        foreach ($output as $line) {
            if (preg_match(self::REGEXP_BSSID, $line, $matches)) {
                $networks[++$current] = [
                    "name" => null,
                    "address" => null,
                    "signal" => null,
                    "channel" => null,
                ];
                $networks[$current]["address"] = $matches[1];
            }
            if (preg_match(self::REGEXP_ESSID, $line, $matches)) {
                $networks[$current]["name"] = $matches[1];
            }
            if (preg_match(self::REGEXP_SIGNAL, $line, $matches)) {
                $networks[$current]["signal"] = (integer) $matches[1];
            }
            if (preg_match(self::REGEXP_CHANNEL, $line, $matches)) {
                $networks[$current]["channel"] = (integer) $matches[1];
            }
        }

        return $networks;
    }
}
