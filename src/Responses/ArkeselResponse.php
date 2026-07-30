<?php

namespace Speso\Ussd\Responses;

use Illuminate\Support\Facades\App;
use Speso\Ussd\Context;
use Speso\Ussd\Contracts\Response;

class ArkeselResponse implements Response
{
    public function respond(string $message, bool $terminating): array
    {
        /** @var Context */ $context = App::make(Context::class);

        return [
            'sessionID' => $context->uid(),
            'userID' => config('services.arkesel.ussd_user_id'),
            'msisdn' => $context->gid(),
            'message' => $message,
            'continueSession' => !$terminating,
        ];
    }
}
