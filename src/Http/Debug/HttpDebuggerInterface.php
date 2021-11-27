<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Http\Debug;

use Psr\Http\Message\RequestInterface;

interface HttpDebuggerInterface
{
    public function debugRequest(RequestInterface $req): void;
}
