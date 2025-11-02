<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

use DateTimeInterface;
use Shlinkio\Shlink\SDK\Utils\ArraySerializable;

use function sprintf;

final class ShortUrlsFilter implements ArraySerializable
{
    private const string TAGS_MODE_ANY = 'any';
    private const string TAGS_MODE_ALL = 'all';

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
        return $this->cloneWithProp('tags', $tags)->cloneWithProp('tagsMode', self::TAGS_MODE_ANY);
    }

    public function containingAllTags(string ...$tags): self
    {
        return $this->cloneWithProp('tags', $tags)->cloneWithProp('tagsMode', self::TAGS_MODE_ALL);
    }

    public function notContainingSomeTags(string ...$excludeTags): self
    {
        return $this->cloneWithProp('excludeTags', $excludeTags)->cloneWithProp('excludeTagsMode', self::TAGS_MODE_ANY);
    }

    public function notContainingAnyTags(string ...$excludeTags): self
    {
        return $this->cloneWithProp('excludeTags', $excludeTags)->cloneWithProp('excludeTagsMode', self::TAGS_MODE_ALL);
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

    public function forDomain(string $domain): self
    {
        return $this->cloneWithProp('domain', $domain);
    }

    public function createdWithApiKey(string $apiKeyName): self
    {
        return $this->cloneWithProp('apiKeyName', $apiKeyName);
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
