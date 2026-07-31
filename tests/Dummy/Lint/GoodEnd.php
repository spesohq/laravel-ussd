<?php

namespace Speso\Ussd\Tests\Dummy\Lint;

use Speso\Ussd\Attributes\Terminate;
use Speso\Ussd\Contracts\State;
use Speso\Ussd\Menu;

#[Terminate]
class GoodEnd implements State
{
    public function render(): Menu
    {
        return Menu::build()->text('End');
    }
}
