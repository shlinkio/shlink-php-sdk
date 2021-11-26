<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Http;

use JsonSerializable;
use Shlinkio\Shlink\SDK\Http\Exception\HttpException;

interface HttpClientInterface
{
    /**
     * @throws HttpException
     */
    public function getFromShlink(string $path, array $query = []): array;

    /**
     * @throws HttpException
     */
    public function callShlinkWithBody(
        string $path,
        string $method,
        array|JsonSerializable $body,
        array $query = [],
    ): array;
}
