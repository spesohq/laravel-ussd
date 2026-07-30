<?php

namespace Speso\Ussd\Responses;

use Speso\Ussd\Contracts\Response;

class MoolreResponse implements Response
{
    public function respond(string $message, bool $terminating): array
    {
        return [
            'message' => $message,
            'reply' => !$terminating,
        ];
    }
}
