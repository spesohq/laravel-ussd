<?php

namespace Speso\Ussd\Responses;

use Speso\Ussd\Contracts\Response;

class SpesoResponse implements Response
{
    public function respond(string $message, bool $terminating): array
    {
        return [
            'message' => $message,
            'action' => $terminating ? 'end' : 'prompt',
        ];
    }
}
