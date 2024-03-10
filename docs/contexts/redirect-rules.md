# Redirect rules

In order to consume the [Redirect rules](https://api-spec.shlink.io/#/Redirect%20rules) context of the API, you need to create a `RedirectRulesClient`, as explained in [Shlink "clients"](/shlink-clients).

```php
use Shlinkio\Shlink\SDK\Http\HttpClient;
use Shlinkio\Shlink\SDK\RedirectRules\RedirectRulesClient;

$redirectRulesClient = new RedirectRulesClient(new HttpClient(...));
```

Once you have it, these are all the operations you can perform:

### Get short URL redirect rules

```php
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

$redirectRulesList = $redirectRulesClient->getShortUrlRedirectRules(ShortUrlIdentifier::fromShortCode('abc123'));

foreach ($redirectRulesList->redirectRules as $rule) {
    echo 'The long URL for rule with priority ' . $rule->priority . ' is ' . $rule->longUrl;
    foreach ($rule->conditions as $condition) {
        echo 'Condition type: ' . $condition->type->value;
        echo 'Condition matching value: ' . $condition->matchValue;
        echo 'Condition matching key: ' . $condition->matchKey;
    }
}
```

### Set short URL redirect rules

You can create redirect rules for a specific short URL in two ways.

From scratch:

```php
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectCondition;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectRuleData;
use Shlinkio\Shlink\SDK\RedirectRules\Model\SetRedirectRules;
use Shlinkio\Shlink\SDK\ShortUrls\Model\Device;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

$newRedirectRules = SetRedirectRules::fromScratch()
    ->withPushedRule(
        RedirectRuleData::forLongUrl('https://example.com/android')
            ->withCondition(RedirectCondition::forDevice(Device::ANDROID)),
    )
    ->withPushedRule(
        RedirectRuleData::forLongUrl('https://example.com/ios-and-french')
            ->withCondition(RedirectCondition::forDevice(Device::IOS))
            ->withCondition(RedirectCondition::forLanguage('fr')),
    );

$redirectRulesList->setShortUrlRedirectRules(ShortUrlIdentifier::fromShortCode('abc123'), $newRedirectRules)
```

From an existing list of redirect rules:

```php
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectCondition;
use Shlinkio\Shlink\SDK\RedirectRules\Model\RedirectRuleData;
use Shlinkio\Shlink\SDK\RedirectRules\Model\SetRedirectRules;
use Shlinkio\Shlink\SDK\ShortUrls\Model\Device;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

$identifier = ShortUrlIdentifier::fromShortCode('abc123');
$redirectRulesList = $redirectRulesClient->getShortUrlRedirectRules($identifier);

$newRedirectRules = SetRedirectRules::fromRedirectRulesList($redirectRulesList)
    ->withoutRule(priority: 1)
    ->withPushedRule(
        RedirectRuleData::forLongUrl('https://example.com/desktop-and-foo-bar-query')
            ->withCondition(RedirectCondition::forDevice(Device::DESKTOP))
            ->withCondition(RedirectCondition::forQueryParam('foo', 'bar')),
    );

$redirectRulesList->setShortUrlRedirectRules($identifier, $newRedirectRules);
```
