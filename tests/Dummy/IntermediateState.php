<?php

namespace Speso\Ussd\Tests\Dummy;

use Speso\Ussd\Attributes\Paginate;
use Speso\Ussd\Attributes\Transition;
use Speso\Ussd\Menu;
use Speso\Ussd\Contracts\State;
use Speso\Ussd\Decisions\Equal;
use Speso\Ussd\Record;
use Speso\Ussd\Traits\WithPagination;

#[Paginate([Equal::class, '#'], [Equal::class, '0'])]
#[Transition(GrandAction::class, [Equal::class, 1], DoTheThing::class)]
class IntermediateState implements State
{
    use WithPagination;

    public function render(Record $record): Menu
    {
        return Menu::build()
            ->text('Pick one...')
            ->when($record->has('wow'), function (Menu $menu) {
                $menu->line('Booooom!');
            })
            ->listing($this->getItems(), page: $this->currentPage(), perPage: $this->perPage());
    }

    public function getItems(): array
    {
        return ['Foo', 'Bar', 'Baz'];
    }

    public function perPage(): int
    {
        return 2;
    }
}
