<?php

namespace Speso\Ussd\Tests\Dummy;

use Speso\Ussd\Attributes\Transition;
use Speso\Ussd\Contracts\InitialState;
use Speso\Ussd\Decisions\Equal;
use Speso\Ussd\Menu;

#[Transition(to: MiddleState::class, match: new Equal(1))]
class StartState implements InitialState
{
    public function render(): Menu
    {
        return Menu::build()->text('Start');
    }
}
