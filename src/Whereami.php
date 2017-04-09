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

namespace Whereami;

use Whereami\Discovery\ScannerDiscovery;

class Whereami
{
    const VERSION = "0.1.0-dev";

    private $provider;
    private $scanner;

    public function __construct(
        Provider $provider = null,
        Scanner $scanner = null
    ) {
        $this->provider = $provider;
        $this->scanner = $scanner ?: (new ScannerDiscovery)->find();
    }

    public function whereami()
    {
        $networks = $this->scanner->scan();
        return $this->provider->process($networks);
    }

    public function whereis(array $networks)
    {
        return $this->provider->process($networks);
    }
}
