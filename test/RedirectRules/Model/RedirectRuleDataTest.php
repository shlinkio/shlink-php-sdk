<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\SDK\RedirectRules\Model;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectCondition;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectRule;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectRuleData;
use Shlinkio\Shlink\SDK\ShortUrls\Model\Device;

class RedirectRuleDataTest extends TestCase
{
    #[Test]
    public function creationFromRedirectRule(): void
    {
        $redirectRule = RedirectRule::fromArray([
            'longUrl' => 'https://example.com',
            'priority' => 3,
            'conditions' => [
                [],
                [],
                [],
            ],
        ]);
        $data = RedirectRuleData::fromRedirectRule($redirectRule);

        self::assertEquals([
            'longUrl' => 'https://example.com',
            'conditions' => [
                RedirectCondition::fromArray([]),
                RedirectCondition::fromArray([]),
                RedirectCondition::fromArray([]),
            ],
        ], $data->jsonSerialize());
    }

    #[Test]
    public function manipulationGeneratesNewInstances(): void
    {
        $data1 = RedirectRuleData::forLongUrl('https://example.com');
        $data2 = $data1->withCondition(RedirectCondition::forLanguage('es'));

        self::assertNotSame($data1, $data2);
    }

    #[Test]
    public function conditionsCanBeAdded(): void
    {
        $data = RedirectRuleData::forLongUrl('https://example.com');
        self::assertCount(0, $data->jsonSerialize()['conditions']);

        $data = $data
            ->withCondition(RedirectCondition::forLanguage('es'))
            ->withCondition(RedirectCondition::forLanguage('en'));
        self::assertCount(2, $data->jsonSerialize()['conditions']);

        $data = $data->withCondition(RedirectCondition::forDevice(Device::IOS));
        self::assertCount(3, $data->jsonSerialize()['conditions']);
    }
}
