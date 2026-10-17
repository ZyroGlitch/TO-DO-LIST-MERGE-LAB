<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_dashboard_shows_empty_state(): void
    {
        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('To Do Dashboard')
            ->assertSee('No tasks yet.');
    }
}