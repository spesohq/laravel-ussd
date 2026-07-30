<?php

namespace Speso\Ussd\Tests\Dummy;

use Speso\Ussd\Record;

class DoTheThing
{
    public function __invoke(Record $record)
    {
        $record->set('pop', 'Hurray!!!!!');
    }
}
