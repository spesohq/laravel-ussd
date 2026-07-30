<?php

namespace Speso\Ussd\Tests\Integration;

use Speso\Ussd\Record;
use Speso\Ussd\Tests\TestCase;
use Speso\Ussd\Tests\Dummy\GrandAction;
use Speso\Ussd\Tests\Dummy\FinishingState;

final class ActionTest extends TestCase
{
    public function test_action_can_run_with_dependency_injection()
    {
        $record = new Record('array', '1234', 'abcd');
        $action = new GrandAction();

        $this->assertEquals(FinishingState::class, $action->execute($record));
    }
}
