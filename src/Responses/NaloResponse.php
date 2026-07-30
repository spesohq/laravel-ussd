<?php

namespace Speso\Ussd\Responses;

use Illuminate\Support\Facades\App;
use Speso\Ussd\Context;
use Speso\Ussd\Contracts\Response;

class NaloResponse implements Response
{
    public function respond(string $message, bool $terminating): array
    {
        /** @var Context */ $context = App::make(Context::class);

        return [
            'USERID' => $context->uid(),
            'MSISDN' => $context->gid(),
            'USERDATA' => $context->input(),
            'MSG' => $message,
            'MSGTYPE' => !$terminating,
        ];
    }
}
