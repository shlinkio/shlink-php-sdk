<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use Shlinkio\Shlink\SDK\Utils\ArraySerializable;

class OrphanVisitsFilter implements ArraySerializable
{
    use VisitsFilterPayloadTrait;

    public function onlyIncludingBaseUrl(): self
    {
        return $this->cloneWithProp('type', OrphanVisitType::BASE_URL->value);
    }

    public function onlyIncludingRegularNotFound(): self
    {
        return $this->cloneWithProp('type', OrphanVisitType::REGULAR_NOT_FOUND->value);
    }

    public function onlyIncludingInvalidShortUrl(): self
    {
        return $this->cloneWithProp('type', OrphanVisitType::INVALID_SHORT_URL->value);
    }
}
