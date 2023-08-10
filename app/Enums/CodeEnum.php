<?php

namespace App\Enums;

use App\Enums\Metas\Description;
use BiiiiiigMonster\LaravelEnum\Concerns\EnumTraits;

/**
 * @method static int SUCCESS()
 * @method static int FAIL()
 * @method mixed description()
 */
enum CodeEnum: int
{
    use EnumTraits;

    #[Description('成功')]
    case SUCCESS = 1;

    #[Description('失败')]
    case FAIL = 0;
}
