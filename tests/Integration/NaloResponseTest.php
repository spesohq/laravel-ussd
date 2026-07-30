<?php

namespace Speso\Ussd\Tests\Integration;

use Speso\Ussd\Context;
use Speso\Ussd\Responses\NaloResponse;
use Speso\Ussd\Tests\Dummy\BeginningState;
use Speso\Ussd\Tests\TestCase;
use Speso\Ussd\Ussd;

final class NaloResponseTest extends TestCase
{
    public function test_nalo_response_can_be_used_via_use_response()
    {
        $this->assertEquals(
            [
                'USERID' => '1234',
                'MSISDN' => '7890',
                'USERDATA' => '1',
                'MSG' => "In the\n#.More",
                'MSGTYPE' => true,
            ],
            Ussd::build(
                Context::create('1234', '7890', '1')
            )
            ->useInitialState(BeginningState::class)
            ->useResponse(NaloResponse::class)
            ->run()
        );
    }
}
