<?php

namespace Speso\Ussd\Tests\Dummy;

use Speso\Ussd\Attributes\Back;
use Speso\Ussd\Attributes\Transition;
use Speso\Ussd\Contracts\State;
use Speso\Ussd\Decisions\Equal;
use Speso\Ussd\Menu;
use Speso\Ussd\Record;

#[Back(match: new Equal(0), callback: [self::class, 'markWentBack'])]
#[Transition(to: EndState::class, match: new Equal(1))]
class MiddleState implements State
{
    public function render(): Menu
    {
        return Menu::build()->text('Middle');
    }

    public function markWentBack(Record $record): void
    {
        $record->set('went_back', true);
    }
}
