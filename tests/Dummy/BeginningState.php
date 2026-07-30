<?php

namespace Speso\Ussd\Tests\Dummy;

use Speso\Ussd\Attributes\Truncate;
use Speso\Ussd\Attributes\Transition;
use Speso\Ussd\Context;
use Speso\Ussd\Contracts\InitialState;
use Speso\Ussd\Decisions\Equal;
use Speso\Ussd\Menu;
use Speso\Ussd\Record;

#[Truncate(18, '#.More', [Equal::class, '#'])]
#[Transition(PetitAction::class, [Equal::class, 1], [self::class, 'callback'])]
class BeginningState implements InitialState
{
    public function render(): Menu
    {
        return Menu::build()->text('In the beginning...');
    }

    public function callback(Record $record, Context $context)
    {
        $record->set('wow', $context->input());
    }
}
