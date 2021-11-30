<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use DateTimeInterface;
use Shlinkio\Shlink\SDK\Utils\ArraySerializable;

use function sprintf;

final class ShortUrlsFilter implements ArraySerializable
{
    private function __construct(private array $query = [])
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function since(DateTimeInterface $since): self
    {
        return $this->cloneWithProp('startDate', $since->format(DateTimeInterface::ATOM));
    }

    public function until(DateTimeInterface $until): self
    {
        return $this->cloneWithProp('endDate', $until->format(DateTimeInterface::ATOM));
    }

    public function searchingBy(string $searchTerm): self
    {
        return $this->cloneWithProp('searchTerm', $searchTerm);
    }

    public function containingTags(string ...$tags): self
    {
        return $this->cloneWithProp('tags', $tags);
    }

    /**
     * @param ShortUrlListOrderFields::* $field
     */
    public function orderingAscBy(string $field): self
    {
        return $this->cloneWithProp('orderBy', sprintf('%s-ASC', $field));
    }

    /**
     * @param ShortUrlListOrderFields::* $field
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
