<?php

namespace Speso\Ussd\Exceptions;

use Speso\Ussd\Contracts\Configurator;

class InvalidConfiguratorException extends UssdException
{
    public function __construct(string $configurator)
    {
        parent::__construct("Invalid configurator, {$configurator} should implement ". Configurator::class);
    }
}
