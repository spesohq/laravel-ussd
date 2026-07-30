<?php

namespace Speso\Ussd\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Speso\Ussd\Record;
use Speso\Ussd\Tests\Dummy\FinishingState;
use Speso\Ussd\Tests\Dummy\GrandAction;
use Speso\Ussd\Tests\Dummy\PetitAction;
use Speso\Ussd\Tests\Dummy\IntermediateState;

final class ActionTest extends TestCase
{
    public function test_action_can_run_without_any_dependency_injection()
    {
        $action = new PetitAction();

        $this->assertEquals(IntermediateState::class, $action->execute());
    }
}
