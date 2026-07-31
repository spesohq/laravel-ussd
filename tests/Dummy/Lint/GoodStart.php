<?php

namespace Speso\Ussd\Tests\Dummy\Lint;

use Speso\Ussd\Attributes\Transition;
use Speso\Ussd\Contracts\InitialState;
use Speso\Ussd\Decisions\Equal;
use Speso\Ussd\Menu;

#[Transition(to: GoodEnd::class, match: new Equal(1))]
#[Transition(to: DeadEnd::class, match: new Equal(2))]
#[Transition(to: BackOnly::class, match: new Equal(3))]
#[Transition(to: BrokenTarget::class, match: new Equal(4))]
#[Transition(to: DuplicateMatches::class, match: new Equal(5))]
class GoodStart implements InitialState
{
    public function render(): Menu
    {
        return Menu::build()->text('Start');
    }
}
