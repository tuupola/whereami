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

interface Provider
{
    /**
     * Process the given data and return location
     *
     * @param array $data
     * @return array
     */
    public function process(array $data, array $options = []);
}
