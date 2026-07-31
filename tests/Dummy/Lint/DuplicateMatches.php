<?php

namespace Speso\Ussd\Tests\Dummy\Lint;

use Speso\Ussd\Attributes\Transition;
use Speso\Ussd\Contracts\State;
use Speso\Ussd\Decisions\Equal;
use Speso\Ussd\Menu;

#[Transition(to: GoodEnd::class, match: new Equal(1))]
#[Transition(to: DeadEnd::class, match: new Equal(1))]
class DuplicateMatches implements State
{
    public function render(): Menu
    {
        return Menu::build()->text('Duplicate matches');
    }
}
