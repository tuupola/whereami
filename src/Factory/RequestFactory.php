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

namespace Whereami\Factory;

use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Nyholm\Psr7\Request as NyholmRequest;
use Slim\Http\Request as SlimRequest;
use Slim\Http\Uri as SlimUri;
use Zend\Diactoros\Request as DiactorosRequest;

use Interop\Http\Factory\RequestFactoryInterface;

final class RequestFactory implements RequestFactoryInterface
{
    public function createRequest($method, $uri)
    {
        if (class_exists(SlimRequest::class)) {
            $uri = SlimUri::createFromString($uri);
            return new SlimRequest($method, $uri);
        }

        if (class_exists(DiactorosRequest::class)) {
            return new DiactorosRequest($method, $uri);
        }

        if (class_exists(NyholmRequest::class)) {
            return new NyholmRequest($method, $uri);
        }

        if (class_exists(GuzzleRequest::class)) {
            return new GuzzleRequest($method, $uri);
        }

        throw new \RuntimeException("No PSR-7 implementation available");
    }
}
