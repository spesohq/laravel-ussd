<?php

namespace Speso\Ussd\Contracts;

interface ContinueState extends State
{
    public function confirm(): Decision;
}
