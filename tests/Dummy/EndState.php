<?php

namespace Speso\Ussd\Tests\Dummy;

use Speso\Ussd\Attributes\Terminate;
use Speso\Ussd\Contracts\State;
use Speso\Ussd\Menu;

#[Terminate]
class EndState implements State
{
    public function render(): Menu
    {
        return Menu::build()->text('End');
    }
}
