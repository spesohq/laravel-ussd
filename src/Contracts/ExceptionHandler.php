<?php

namespace Speso\Ussd\Contracts;

use Exception;

interface ExceptionHandler
{
    public function handle(Exception $exception): string;
}
