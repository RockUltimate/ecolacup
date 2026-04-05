<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class ConsoleScheduleTest extends TestCase
{
    public function test_scheduler_registers_maintenance_commands(): void
    {
        $events = collect(app(Schedule::class)->events());

        $this->assertTrue($events->contains(
            fn ($event) => str_contains((string) $event->command, 'auth:clear-resets')
                && $event->expression === '*/15 * * * *'
        ));

        $this->assertTrue($events->contains(
            fn ($event) => str_contains((string) $event->command, 'queue:prune-batches --hours=48')
                && $event->expression === '15 2 * * *'
        ));

        $this->assertTrue($events->contains(
            fn ($event) => str_contains((string) $event->command, 'queue:prune-failed --hours=168')
                && $event->expression === '30 2 * * *'
        ));
    }
}
