<?php

namespace Speso\Ussd\Tests\Dummy;

use Speso\Ussd\Contracts\Action;
use Speso\Ussd\Record;

class GrandAction implements Action
{
    public function execute(Record $record): string
    {
        $record->set('magic', 'abracadabra');

        return FinishingState::class;
    }
}
