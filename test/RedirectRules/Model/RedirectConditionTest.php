<?php

declare(strict_types=1);

namespace RedirectRules\Model;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectCondition;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectConditionType;
use Shlinkio\Shlink\SDK\ShortUrls\Model\Device;

class RedirectConditionTest extends TestCase
{
    #[Test]
    #[TestWith([
        RedirectConditionType::LANGUAGE->value,
        RedirectConditionType::LANGUAGE,
        RedirectConditionType::LANGUAGE->value,
    ])]
    #[TestWith([
        RedirectConditionType::DEVICE->value,
        RedirectConditionType::DEVICE,
        RedirectConditionType::DEVICE->value,
    ])]
    #[TestWith([
        RedirectConditionType::QUERY_PARAM->value,
        RedirectConditionType::QUERY_PARAM,
        RedirectConditionType::QUERY_PARAM->value,
    ])]
    #[TestWith(['something-else', RedirectConditionType::UNKNOWN, 'something-else'])]
    public function expectedTypeAndOriginalTypeAreSet(
        string $providedType,
        RedirectConditionType $expectedType,
        string $expectedRawType,
    ): void {
        $condition = RedirectCondition::fromArray(['type' => $providedType]);

        self::assertEquals($expectedType, $condition->type);
        self::assertEquals($expectedRawType, $condition->jsonSerialize()['type']);
    }

    #[Test]
    public function forQueryParamCreatesExpectedCondition(): void
    {
        $condition = RedirectCondition::forQueryParam('foo', 'bar');

        self::assertEquals(RedirectConditionType::QUERY_PARAM, $condition->type);
        self::assertEquals('foo', $condition->matchKey);
        self::assertEquals('bar', $condition->matchValue);
    }

    #[Test]
    public function forLanguageCreatesExpectedCondition(): void
    {
        $condition = RedirectCondition::forLanguage('es-ES');

        self::assertEquals(RedirectConditionType::LANGUAGE, $condition->type);
        self::assertEquals('es-ES', $condition->matchValue);
        self::assertNull($condition->matchKey);
    }

    #[Test]
    public function forDeviceCreatesExpectedCondition(): void
    {
        $condition = RedirectCondition::forDevice(Device::IOS);

        self::assertEquals(RedirectConditionType::DEVICE, $condition->type);
        self::assertEquals(Device::IOS->value, $condition->matchValue);
        self::assertNull($condition->matchKey);
    }
}
