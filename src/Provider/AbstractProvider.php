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
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Whereami\Exception\NotFoundException;
use Whereami\Exception\BadRequestException;
use Whereami\Factory\HttpClientFactory;
use Whereami\Provider;

abstract class AbstractProvider implements Provider
{
    protected $apikey;
    private $response;
    private $httpClient;
    private $requestFactory;

    public function __construct(
        $apikey,
        HttpClient $httpClient = null,
        RequestFactory $requestFactory = null
    ) {
        $this->apikey = $apikey;
        $this->httpClient = $httpClient ?: (new HttpClientFactory)->create();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
    }

    public function process(array $data, array $options = [])
    {
        $endpoint = $this->endpoint();
        $headers = [];
        $body = $this->transform($data);
        $request = $this->requestFactory->createRequest("POST", $endpoint, $headers, $body);
        $request = $this->addRequestHeaders($request);
        $this->response = $this->httpClient->sendRequest($request);

        if ($this->response->getStatusCode() >= 400 && $this->response->getStatusCode() < 600) {
            $this->handleError($this->response);
        }

        return $this->parse((string) $this->response->getBody());
    }

    public function lastResponse()
    {
        return $this->response;
    }

    protected function handleError(ResponseInterface $response)
    {
        if (404 === $response->getStatusCode()) {
            throw new NotFoundException("No matches found", 404);
        }
        $data = json_decode((string) $response->getBody(), true);
        throw new BadRequestException($data["error"]["message"]);
    }

    protected function addRequestHeaders(RequestInterface $request)
    {
        return $request
            ->withHeader("Accept", "application/json")
            ->withHeader("Content-Type", "application/json; charset=utf-8");
    }

    protected function endpoint()
    {
        return $this->endpoint .= "?" . http_build_query(["key" => $this->apikey]);
    }

    abstract protected function transform($data = []);

    abstract protected function parse($json);
}
