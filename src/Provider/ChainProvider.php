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

use Whereami\Provider;

final class ChainProvider implements Provider
{
    private $providers;

    public function __construct(array $providers, $options = [])
    {
        $this->providers = $providers;
    }

    public function process(array $data = [], array $options = [])
    {
        return [];
    }
}
