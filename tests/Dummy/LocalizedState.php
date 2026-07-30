<?php

namespace Speso\Ussd\Tests\Dummy;

use Speso\Ussd\Attributes\Transition;
use Speso\Ussd\Contracts\InitialState;
use Speso\Ussd\Decisions\Fallback;
use Speso\Ussd\Menu;
use Speso\Ussd\Record;

#[Transition(to: self::class, match: new Fallback(), callback: [self::class, 'switchToFrench'])]
class LocalizedState implements InitialState
{
    public function render(): Menu
    {
        return Menu::build()->trans('greeting.hello');
    }

    public function switchToFrench(Record $record): void
    {
        $record->setLocale('fr');
    }
}
