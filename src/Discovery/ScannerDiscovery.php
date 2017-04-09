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

namespace Whereami\Discovery;

use Whereami\Discovery;
use Whereami\Scanner\AirportScanner;
use Whereami\Scanner\IwlistScanner;

final class ScannerDiscovery implements Discovery
{
    public function find()
    {
        if (file_exists(AirportScanner::BINARY)) {
            return new AirportScanner;
        }
        if (file_exists(IwlistScanner::BINARY)) {
            return new IwlistScanner;
        }
        throw new \RuntimeException("No wifi scanners found.");
    }
}
