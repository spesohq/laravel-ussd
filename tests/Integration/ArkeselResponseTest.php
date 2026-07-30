<?php

namespace Speso\Ussd\Tests\Integration;

use Speso\Ussd\Context;
use Speso\Ussd\Responses\ArkeselResponse;
use Speso\Ussd\Tests\Dummy\BeginningState;
use Speso\Ussd\Tests\TestCase;
use Speso\Ussd\Ussd;

final class ArkeselResponseTest extends TestCase
{
    public function test_arkesel_response_can_be_used_via_use_response()
    {
        config(['services.arkesel.ussd_user_id' => 'USSD_TESTING']);

        $this->assertEquals(
            [
                'sessionID' => '1234',
                'userID' => 'USSD_TESTING',
                'msisdn' => '7890',
                'message' => "In the\n#.More",
                'continueSession' => true,
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useResponse(ArkeselResponse::class)
            ->run()
        );
    }
}
