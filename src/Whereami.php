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
use Whereami\Provider\EmulatedProvider;

class Whereami
{
    const VERSION = "0.3.0";

    private $provider;
    private $scanner;
    private $adapter;

    public function __construct($provider, Scanner $scanner = null)
    {
        if ($provider instanceof Provider) {
            $this->provider = $provider;
        } elseif ($provider instanceof Adapter) {
            $this->provider = new EmulatedProvider($provider);
        } else {
            throw new \RuntimeException(
                "Provider must implement either Wheremami\\Provider or Whereami\\Adapter."
            );
        }

        try {
            /* If scanner was not provided try to discover one. */
            $this->scanner = $scanner ?: (new ScannerDiscovery)->find();
        } catch (\RuntimeException $exception) {
            /* We should be able to continue without scanner. */
        }
    }

    public function whereami()
    {
        /* Emulated provider does not handle network information. */
        if ($this->provider instanceof EmulatedProvider) {
            return $this->provider->process([]);
        }
        $networks = $this->scanner->scan();
        return $this->provider->process($networks);
    }

    public function whereis(array $networks)
    {
        return $this->provider->process($networks);
    }
}
