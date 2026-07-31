<?php

namespace Speso\Ussd\Tests\Dummy\Lint;

use Speso\Ussd\Contracts\State;
use Speso\Ussd\Menu;

class DeadEnd implements State
{
    public function render(): Menu
    {
        return Menu::build()->text('Dead end');
    }
}
