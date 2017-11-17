<?php

namespace Kjdion84\Turtle\Traits;

use Carbon\Carbon;

trait InTime
{
    public function getCreatedAtAttribute($value)
    {
        return $this->inTime($value);
    }

    public function getUpdatedAtAttribute($value)
    {
        return $this->inTime($value);
    }

    public function getDeletedAtAttribute($value)
    {
        return $this->inTime($value);
    }

    // convert database time to user timezone
    public function inTime($value)
    {
        return Carbon::parse($value)->tz(auth()->check() ? auth()->user()->timezone : config('app.timezone'))->toDateTimeString();
    }
}