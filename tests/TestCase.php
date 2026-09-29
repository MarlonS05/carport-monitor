<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Concerns\AuthenticatesUsers;

abstract class TestCase extends BaseTestCase
{
    use AuthenticatesUsers;
}
