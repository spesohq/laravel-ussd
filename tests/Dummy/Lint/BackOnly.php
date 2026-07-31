<?php

namespace Speso\Ussd\Tests\Dummy\Lint;

use Speso\Ussd\Attributes\Back;
use Speso\Ussd\Contracts\State;
use Speso\Ussd\Decisions\Equal;
use Speso\Ussd\Menu;

#[Back(match: new Equal(0))]
class BackOnly implements State
{
    public function render(): Menu
    {
        return Menu::build()->text('Back only');
    }
}
