<?php

namespace Cesargb\LaravelLog\Test;

use Illuminate\Console\Scheduling\Schedule;

class ScheduleTest extends TestCase
{
    public function testHasSchedule()
    {
        $this->assertTrue($this->scheduleRegistered());
    }

    private function scheduleRegistered(): bool
    {
        return !is_null($this->schedule());
    }

    private function schedule()
    {
        return collect(app(Schedule::class)
            ->events())
            ->first(fn ($s) => $s->command, ' rotate:logs');
    }
}
