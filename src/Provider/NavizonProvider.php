<?php

namespace Whereami\Provider;

use Whereami\Provider;

final class NavizonProvider implements Provider
{
    private $endpoint;
    private $apikey;

    public function __construct($apikey, $options = [])
    {
        $this->apikey = $apikey;
    }

    public function process(array $data = [], array $options = [])
    {
        return [];
    }
}
