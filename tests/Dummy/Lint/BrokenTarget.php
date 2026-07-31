<?php

namespace Speso\Ussd\Tests\Dummy\Lint;

use Speso\Ussd\Attributes\Terminate;
use Speso\Ussd\Attributes\Transition;
use Speso\Ussd\Contracts\State;
use Speso\Ussd\Decisions\Equal;
use Speso\Ussd\Menu;

#[Terminate]
#[Transition(to: 'Speso\Ussd\Tests\Dummy\Lint\NoSuchClass', match: new Equal(1))]
class BrokenTarget implements State
{
    public function render(): Menu
    {
        return Menu::build()->text('Broken target');
    }
}
