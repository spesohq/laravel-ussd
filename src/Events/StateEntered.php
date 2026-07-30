<?php

namespace Speso\Ussd\Events;

use Speso\Ussd\Context;

class StateEntered
{
    public function __construct(
        public string $state,
        public Context $context
    ) {
    }
}
