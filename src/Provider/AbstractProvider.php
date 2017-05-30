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

use Http\Client\HttpClient;
use Interop\Http\Factory\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Tuupola\Http\Factory\RequestFactory;
use Tuupola\Http\Factory\StreamFactory;
use Whereami\Exception\NotFoundException;
use Whereami\Exception\BadRequestException;
use Whereami\Factory\HttpClientFactory;
use Whereami\Provider;

abstract class AbstractProvider implements Provider
{
    protected $apikey;
    protected $httpClient;
    protected $requestFactory;
    protected $streamFactory;

    public function __construct(
        $apikey,
        HttpClient $httpClient = null,
        RequestFactory $requestFactory = null,
        StreamFactory $streamFactory = null
    ) {
        $this->apikey = $apikey;
        $this->httpClient = $httpClient ?: (new HttpClientFactory)->create();
        $this->requestFactory = $requestFactory ?: new RequestFactory;
        $this->streamFactory = $streamFactory ?: new StreamFactory;
    }

    public function process(array $data, array $options = [])
    {
        $endpoint = $this->endpoint();
        $body = $this->streamFactory->createStream($this->transform($data));
        $request = $this->requestFactory->createRequest("POST", $endpoint)->withBody($body);
        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 600) {
            $this->handleError($response);
        }

        return $this->parse((string) $response->getBody());
    }

    protected function handleError(ResponseInterface $response)
    {
        if (404 === $response->getStatusCode()) {
            throw new NotFoundException("No matches found", 404);
        }
        $data = json_decode((string) $response->getBody(), true);
        throw new BadRequestException($data["error"]["message"]);
    }


    protected function endpoint()
    {
        return $this->endpoint .= "?" . http_build_query(["key" => $this->apikey]);
    }

    abstract protected function transform($data = []);

    abstract protected function parse($json);
}
