<?php

namespace Speso\Ussd\Events;

use Speso\Ussd\Context;

class SessionTerminated
{
    public function __construct(
        public Context $context
    ) {
    }
}
