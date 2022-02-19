<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Model;

use Shlinkio\Shlink\SDK\Utils\ArraySerializable;

use function explode;
use function in_array;
use function sprintf;

final class TagsFilter implements ArraySerializable
{
    private function __construct(private array $query = [])
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function searchingBy(string $searchTerm): self
    {
        return $this->cloneWithProp('searchTerm', $searchTerm);
    }

    /**
     * @param TagsListOrderFields::* $field
     */
    public function orderingAscBy(string $field): self
    {
        return $this->cloneWithProp('orderBy', sprintf('%s-ASC', $field));
    }

    /**
     * @param TagsListOrderFields::* $field
     */
    public function orderingDescBy(string $field): self
    {
        return $this->cloneWithProp('orderBy', sprintf('%s-DESC', $field));
    }

    private function cloneWithProp(string $prop, mixed $value): self
    {
        $clone = new self($this->query);
        $clone->query[$prop] = $value;

        return $clone;
    }

    public function toArray(): array
    {
        return $this->query;
    }

    public function shouldPaginateRequest(): bool
    {
        // Due to an issue on Shlink, ordering by anything other than the tag name makes the request equally slow,
        // no matter the size of the page. Because of that, when ordering by those fields, it's better to load the
        // whole dataset at once, until that issue is fixed.

        if (! isset($this->query['orderBy'])) {
            return true;
        }

        [$field] = explode('-', $this->query['orderBy']);
        $orderFieldsThatShouldNotPaginate = [TagsListOrderFields::SHORT_URLS_COUNT, TagsListOrderFields::VISITS_COUNT];

        return ! in_array($field, $orderFieldsThatShouldNotPaginate, true);
    }
}
