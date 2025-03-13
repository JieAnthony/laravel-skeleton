<?php

namespace App\Enums;

use App\Enums\Metas\Description;
use BiiiiiigMonster\LaravelEnum\Concerns\EnumTraits;

/**
 * @method static int DIRECTORY()
 * @method static int MENU()
 * @method static int BUTTON()
 * @method mixed description()
 */
enum PermissionTypeEnum: int
{
    use EnumTraits;

    #[Description('目录')]
    case DIRECTORY = 1;

    #[Description('菜单')]
    case MENU = 2;

    #[Description('按钮')]
    case BUTTON = 3;
}
