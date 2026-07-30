<?php

namespace Speso\Ussd\Responses;

use Speso\Ussd\Contracts\Response;

class AfricasTalkingResponse implements Response
{
    public function respond(string $message, bool $terminating): string
    {
        return ($terminating ? 'END' : 'CON')." {$message}";
    }
}
