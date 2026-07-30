<?php

namespace Speso\Ussd\Contracts;

interface Response
{
    public function respond(string $message, bool $terminating): mixed;
}
