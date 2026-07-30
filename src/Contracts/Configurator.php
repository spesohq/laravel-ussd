<?php

namespace Speso\Ussd\Contracts;

use Speso\Ussd\Ussd;

interface Configurator
{
    public function configure(Ussd $ussd): void;
}
