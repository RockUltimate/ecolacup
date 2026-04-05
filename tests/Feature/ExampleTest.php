<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainModels;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use CreatesDomainModels;
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $udalost = $this->createUdalost([
            'nazev' => 'Jarni pohar',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Jarni pohar');
        $response->assertSee(route('udalosti.show', $udalost, absolute: false));
    }
}
