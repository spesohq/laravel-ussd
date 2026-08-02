<?php

namespace Speso\Ussd\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Speso\Ussd\Responses\AfricasTalkingResponse;
use Speso\Ussd\Responses\MoolreResponse;
use Speso\Ussd\Responses\NsanoResponse;
use Speso\Ussd\Responses\SpesoResponse;

final class ResponseTest extends TestCase
{
    public function test_africas_talking_response_prefixes_continuing_messages_with_con()
    {
        $response = new AfricasTalkingResponse();

        $this->assertEquals('CON Welcome', $response->respond('Welcome', false));
    }

    public function test_africas_talking_response_prefixes_terminating_messages_with_end()
    {
        $response = new AfricasTalkingResponse();

        $this->assertEquals('END Bye', $response->respond('Bye', true));
    }

    public function test_nsano_response_uses_input_action_when_continuing()
    {
        $response = new NsanoResponse();

        $this->assertEquals(
            ['USSDResp' => ['action' => 'input', 'menus' => '', 'title' => 'Welcome']],
            $response->respond('Welcome', false)
        );
    }

    public function test_nsano_response_uses_prompt_action_when_terminating()
    {
        $response = new NsanoResponse();

        $this->assertEquals(
            ['USSDResp' => ['action' => 'prompt', 'menus' => '', 'title' => 'Bye']],
            $response->respond('Bye', true)
        );
    }

    public function test_moolre_response_keeps_reply_true_when_continuing()
    {
        $response = new MoolreResponse();

        $this->assertEquals(
            ['message' => 'Welcome', 'reply' => true],
            $response->respond('Welcome', false)
        );
    }

    public function test_moolre_response_sets_reply_false_when_terminating()
    {
        $response = new MoolreResponse();

        $this->assertEquals(
            ['message' => 'Bye', 'reply' => false],
            $response->respond('Bye', true)
        );
    }

    public function test_speso_response_uses_prompt_action_when_continuing()
    {
        $response = new SpesoResponse();

        $this->assertEquals(
            ['message' => 'Welcome', 'action' => 'prompt'],
            $response->respond('Welcome', false)
        );
    }

    public function test_speso_response_uses_end_action_when_terminating()
    {
        $response = new SpesoResponse();

        $this->assertEquals(
            ['message' => 'Bye', 'action' => 'end'],
            $response->respond('Bye', true)
        );
    }
}
