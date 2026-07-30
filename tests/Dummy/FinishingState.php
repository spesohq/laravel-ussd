<?php

namespace Speso\Ussd\Tests\Dummy;

use Speso\Ussd\Attributes\Terminate;
use Speso\Ussd\Contracts\State;
use Speso\Ussd\Menu;
use Speso\Ussd\Record;

#[Terminate]
class FinishingState implements State
{
    public function render(Record $record): Menu
    {
        [$magic, $pop] = $record->getMany(['magic', 'pop']);

        return Menu::build()->line('Tadaa...')->text($magic)->lineBreak()->text($pop);
    }
}
