<?php

namespace Speso\Ussd\Contracts;

interface Decision
{
    public function decide(string $actual): bool;
}
