<?php

namespace Speso\Ussd\Tests\Dummy;

use Speso\Ussd\Contracts\Action;

class PetitAction implements Action
{
    public function execute(): string
    {
        return IntermediateState::class;
    }
}
