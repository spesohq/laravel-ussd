<?php

namespace Speso\Ussd\Tests\Dummy\Lint;

use Speso\Ussd\Contracts\ContinueState;
use Speso\Ussd\Contracts\Decision;
use Speso\Ussd\Decisions\Equal;
use Speso\Ussd\Menu;

class ContinueOnly implements ContinueState
{
    public function render(): Menu
    {
        return Menu::build()->text('Continue?');
    }

    public function confirm(): Decision
    {
        return new Equal(1);
    }
}
