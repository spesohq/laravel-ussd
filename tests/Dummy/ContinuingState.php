<?php

namespace Speso\Ussd\Tests\Dummy;

use Speso\Ussd\Contracts\ContinueState;
use Speso\Ussd\Contracts\Decision;
use Speso\Ussd\Decisions\Equal;
use Speso\Ussd\Menu;

class ContinuingState implements ContinueState
{
    public function render(): Menu
    {
        return Menu::build()
            ->line('Wanna continue?')
            ->listing(['Yes'])
            ->text('Any to start');
    }

    public function confirm(): Decision
    {
        return new Equal(1);
    }
}
