<?php

declare(strict_types=1);

namespace RedirectRules\Model;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectConditionType;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectRuleData;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectRulesList;
use Shlinkio\Shlink\SDK\RedirectRules\Model\SetRedirectRules;
use Shlinkio\Shlink\SDK\ShortUrls\Model\Device;

use function json_decode;
use function json_encode;

class SetRedirectRulesTest extends TestCase
{
    #[Test]
    public function creationFromRedirectRulesList(): void
    {
        $list = RedirectRulesList::fromArray([
            'defaultLongUrl' => 'https://example.com/default',
            'redirectRules' => [
                [
                    'longUrl' => 'https://example.com/android',
                    'priority' => 1,
                    'conditions' => [
                        [
                            'type' => RedirectConditionType::DEVICE->value,
                            'matchValue' => Device::ANDROID->value,
                            'matchKey' => null,
                        ],
                    ],
                ],
                [
                    'longUrl' => 'https://example.com/freanch-and-foo-bar-query',
                    'priority' => 2,
                    'conditions' => [
                        [
                            'type' => RedirectConditionType::LANGUAGE->value,
                            'matchValue' => 'fr',
                            'matchKey' => null,
                        ],
                        [
                            'type' => RedirectConditionType::QUERY_PARAM->value,
                            'matchValue' => 'bar',
                            'matchKey' => 'foo',
                        ],
                    ],
                ],
            ],
        ]);

        $setRules = SetRedirectRules::fromRedirectRulesList($list);

        self::assertEquals([
            'redirectRules' => [
                [
                    'longUrl' => 'https://example.com/android',
                    'conditions' => [
                        [
                            'type' => RedirectConditionType::DEVICE->value,
                            'matchValue' => Device::ANDROID->value,
                            'matchKey' => null,
                        ],
                    ],
                ],
                [
                    'longUrl' => 'https://example.com/freanch-and-foo-bar-query',
                    'conditions' => [
                        [
                            'type' => RedirectConditionType::LANGUAGE->value,
                            'matchValue' => 'fr',
                            'matchKey' => null,
                        ],
                        [
                            'type' => RedirectConditionType::QUERY_PARAM->value,
                            'matchValue' => 'bar',
                            'matchKey' => 'foo',
                        ],
                    ],
                ],
            ],
        ], json_decode(json_encode($setRules->jsonSerialize()), associative: true));
    }

    #[Test]
    public function manipulationGeneratesNewInstances(): void
    {
        $setRules1 = SetRedirectRules::fromScratch();
        $setRules2 = $setRules1->withPushedRule(RedirectRuleData::forLongUrl('https://example.com'));
        $setRules3 = $setRules1->withoutRule(1);

        self::assertNotSame($setRules1, $setRules2);
        self::assertNotSame($setRules1, $setRules3);
        self::assertNotSame($setRules2, $setRules3);
    }

    #[Test]
    public function rulesCanBeAddedOrRemoved(): void
    {
        $setRules = SetRedirectRules::fromScratch()
            ->withPushedRule(RedirectRuleData::forLongUrl('https://example.com/first'))
            ->withPushedRule(RedirectRuleData::forLongUrl('https://example.com/second'));
        self::assertEquals([
            'redirectRules' => [
                RedirectRuleData::forLongUrl('https://example.com/first'),
                RedirectRuleData::forLongUrl('https://example.com/second'),
            ],
        ], $setRules->jsonSerialize());

        $setRules = $setRules->withRule(2, RedirectRuleData::forLongUrl('https://example.com/third'));
        self::assertEquals([
            'redirectRules' => [
                RedirectRuleData::forLongUrl('https://example.com/first'),
                RedirectRuleData::forLongUrl('https://example.com/third'),
                RedirectRuleData::forLongUrl('https://example.com/second'),
            ],
        ], $setRules->jsonSerialize());

        $setRules = $setRules->withoutRule(1);
        self::assertEquals([
            'redirectRules' => [
                RedirectRuleData::forLongUrl('https://example.com/third'),
                RedirectRuleData::forLongUrl('https://example.com/second'),
            ],
        ], $setRules->jsonSerialize());
    }
}
