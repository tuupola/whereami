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

final class CoreLocationAdapter implements Adapter
{
    private $command;

    const BINARY = "/usr/local/bin/CoreLocationCLI";

    public function __construct(
        $command = self::BINARY . " -once YES -format '%latitude,%longitude,%h_accuracy'"
    ) {
        $this->command = $command;
    }

    public function process(array $options = [])
    {
        exec($this->command, $output);
        return $this->parse($output);
    }

    private function parse(array $output)
    {
        $data = explode(",", $output[0]);
        return [
            "latitude" => (float) $data[0],
            "longitude" => (float) $data[1],
            "accuracy" => (integer) $data[2],
        ];
    }
}
