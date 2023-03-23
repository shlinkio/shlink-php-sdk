<?php

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

enum Device: string
{
    case ANDROID = 'android';
    case IOS = 'ios';
    case DESKTOP = 'desktop';
}
