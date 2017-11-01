<?php

/* bread_model_namespace */

use Illuminate\Database\Eloquent\Model;
use Kjdion84\Turtle\Traits\InTime;

class bread_model_class extends Model
{
    use InTime;

    protected $fillable = ["/* bread_fillable */"];
}