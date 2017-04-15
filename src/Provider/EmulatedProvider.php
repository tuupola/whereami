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

use Whereami\Adapter;
use Whereami\Provider;

final class EmulatedProvider implements Provider
{
    private $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function process(array $data, array $options = [])
    {
        if (count($data)) {
            trigger_error("Emulated provider ignores wifi network data.", E_USER_WARNING);
        }
        return $this->adapter->process();
    }
}
