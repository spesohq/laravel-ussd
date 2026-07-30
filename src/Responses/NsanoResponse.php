<?php

namespace Speso\Ussd\Responses;

use Speso\Ussd\Contracts\Response;

class NsanoResponse implements Response
{
    public function respond(string $message, bool $terminating): array
    {
        return [
            'USSDResp' => [
                'action' => $terminating ? 'prompt' : 'input',
                'menus' => '',
                'title' => $message,
            ],
        ];
    }
}
