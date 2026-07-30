<?php

namespace Speso\Ussd\Tests\Dummy;

use Speso\Ussd\Attributes\Truncate;
use Speso\Ussd\Attributes\Paginate;
use Speso\Ussd\Attributes\Terminate;
use Speso\Ussd\Contracts\InitialState;
use Speso\Ussd\Decisions\Equal;
use Speso\Ussd\Menu;
use Speso\Ussd\Traits\WithPagination;

#[Terminate]
#[Truncate(150, '#.More', [Equal::class, '#'])]
#[Paginate([Equal::class, '#'], [Equal::class, '0'])]
class SophisticatedState implements InitialState
{
    use WithPagination;

    public function render(): Menu
    {
        return Menu::build()
            ->line('In the sophisticated world')
            ->listing($this->getItems(), page: $this->currentPage(), perPage: $this->perPage())
            ->unless($this->isLastPage(), fn (Menu $menu) => $menu->text('#.Next'));
    }

    public function getItems(): array
    {
        return [
            'The quick brown fox jumps over the lazy dog.',
            'A journey of a thousand miles begins with a single step.',
            'Success is not final, failure is not fatal: It is the courage to continue that counts.',
            'In the middle of difficulty lies opportunity.',
            'The only way to do great work is to love what you do.',
            'Believe you can and you\'re halfway there.',
            'The future belongs to those who believe in the beauty of their dreams.',
            'Don\'t watch the clock; do what it does. Keep going.',
            'The best way to predict the future is to create it.',
            'Dream big and dare to fail.',
        ];
    }

    public function perPage(): int
    {
        return 3;
    }
}
