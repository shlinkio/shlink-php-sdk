<?php

namespace Shlinkio\Shlink\SDK\ShortUrls\Model;

enum Device: string
{
    case ANDROID = 'android';
    case IOS = 'ios';
    case MOBILE = 'mobile';
    case WINDOWS = 'windows';
    case MACOS = 'macos';
    case LINUX = 'linux';
    case CHROMEOS = 'chromeos';
    case DESKTOP = 'desktop';
}
