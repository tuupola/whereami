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

use Whereami\Whereami;

use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\HeaderSetPlugin;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;

class HttpClientFactory
{
    private $httpClient;
    private $plugins = [];

    public function __construct(
        HttpClient $httpClient = null,
        array $options = []
    ) {
        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->options = $options;
    }

    public function create()
    {
        $plugins = [
            //new ErrorPlugin,
            new HeaderSetPlugin([
                "User-Agent" => "whereami/" .  Whereami::VERSION,
            ])
        ];

        $plugins = array_merge($plugins, $this->plugins);

        return new PluginClient($this->httpClient, $plugins);
    }

    public function addPlugin(Plugin $plugin)
    {
        $this->plugins[] = $plugin;
        return $this;
    }
}
