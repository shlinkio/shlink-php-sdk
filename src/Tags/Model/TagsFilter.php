<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Tags\Model;

use Shlinkio\Shlink\SDK\Utils\ArraySerializable;

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
}
