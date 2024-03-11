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

    public function containingSomeTags(string ...$tags): self
    {
        return $this->cloneWithProp('tags', $tags)->cloneWithProp('tagsMode', 'any');
    }

    public function containingAllTags(string ...$tags): self
    {
        return $this->cloneWithProp('tags', $tags)->cloneWithProp('tagsMode', 'all');
    }

    public function excludingMaxVisitsReached(): self
    {
        return $this->cloneWithProp('excludeMaxVisitsReached', 'true');
    }

    public function excludingPastValidUntil(): self
    {
        return $this->cloneWithProp('excludePastValidUntil', 'true');
    }

    public function orderingAscBy(ShortUrlListOrderField $field): self
    {
        return $this->cloneWithProp('orderBy', sprintf('%s-ASC', $field->value));
    }

    public function orderingDescBy(ShortUrlListOrderField $field): self
    {
        return $this->cloneWithProp('orderBy', sprintf('%s-DESC', $field->value));
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
