<?php

namespace Kjdion84\Turtle\Models;

use Illuminate\Database\Eloquent\Model;
use Kjdion84\Turtle\Traits\InTime;

class Billing extends Model
{
    use InTime;

    protected $fillable = ['user_id', 'plan_name', 'amount', 'cc_last4'];
}