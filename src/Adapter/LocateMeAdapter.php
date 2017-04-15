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

namespace Whereami\Adapter;

use Whereami\Adapter;

final class LocateMeAdapter implements Adapter
{
    private $command;

    const BINARY = "/usr/local/bin/LocateMe";

    public function __construct($command = self::BINARY . " -f '{LAT},{LON},{HAC}'")
    {
        $this->command = $command;
    }

    public function process(array $options = [])
    {
        exec($this->command, $output);
        return $this->parse($output);
    }

    private function parse(array $output = [])
    {
        $data = explode(",", $output[0]);
        return [
            "latitude" => (float) $data[0],
            "longitude" => (float) $data[1],
            "accuracy" => (integer) $data[2],
        ];
    }
}
