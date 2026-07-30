<?php

namespace Speso\Ussd\Tests\Dummy;

use Speso\Ussd\Attributes\Back;
use Speso\Ussd\Attributes\Transition;
use Speso\Ussd\Contracts\InitialState;
use Speso\Ussd\Decisions\Equal;
use Speso\Ussd\Menu;

#[Back(match: new Equal(0))]
#[Transition(to: EndState::class, match: new Equal(0))]
class LoneState implements InitialState
{
    public function render(): Menu
    {
        return Menu::build()->text('Lone');
    }
}
