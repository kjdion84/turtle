<?php

namespace Kjdion84\Turtle\Models;

use Illuminate\Database\Eloquent\Model;
use Kjdion84\Turtle\Traits\InTime;

class Activity extends Model
{
    use InTime;

    protected $fillable = ['user_id', 'model_id', 'model_class', 'data', 'log'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}