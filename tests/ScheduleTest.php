<?php

namespace Cesargb\LaravelLog\Test;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Str;

class ScheduleTest extends TestCase
{
    public function test_has_schedule()
    {
        $this->assertNotNull($this->scheduleEventRotateLogs());
    }

    private function scheduleEventRotateLogs(): ?Event
    {
        return collect(app(Schedule::class)->events())
            ->first(fn(Event $s) => Str::endsWith($s->command, ' rotate:logs'));
    }
}
