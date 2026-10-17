<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_root_route_redirects(): void
    {
        $this->get('/')
            ->assertRedirect('/dashboard');
    }
}