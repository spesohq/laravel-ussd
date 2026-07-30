<?php

namespace Speso\Ussd\Exceptions;

use Speso\Ussd\Contracts\InitialAction;
use Speso\Ussd\Contracts\InitialState;

class InvalidInitialStateException extends UssdException
{
    public function __construct(string $state)
    {
        parent::__construct("Invalid initial state, {$state} should implement ".InitialState::class." or ".InitialAction::class);
    }
}
