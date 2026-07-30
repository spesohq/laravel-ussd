<?php

namespace Speso\Ussd\Tests\Dummy;

use Speso\Ussd\Contracts\Configurator;
use Speso\Ussd\Ussd;

class CogConfigurator implements Configurator
{
    public function __construct(
        private string $operator = 'Default'
    ) { }

    public function configure(Ussd $ussd): void
    {
        $ussd->useResponse(function (string $message, string $terminating) {
            return ['action' => $terminating ? 'prompt' : 'input', 'operator' => $this->operator, 'message' => $message];
        });
    }
}
