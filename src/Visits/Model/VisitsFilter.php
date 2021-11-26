<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\SDK\Visits\Model;

use DateTimeInterface;
use Shlinkio\Shlink\SDK\Utils\ArraySerializable;

final class VisitsFilter implements ArraySerializable
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

    public function excludingBots(): self
    {
        return $this->cloneWithProp('excludeBots', 'true');
    }

    private function cloneWithProp(string $prop, string $value): self
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
