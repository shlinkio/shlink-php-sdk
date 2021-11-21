<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Http;

use Psr\Http\Message\ResponseInterface;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

interface HttpClientInterface
{
    /**
     * @throws HttpException
     */
    public function getFromShlink(string $path, array $query = []): ResponseInterface;

    /**
     * @throws HttpException
     */
    public function callShlinkWithBody(string $path, string $method, array $body, array $query = []): ResponseInterface;
}
