<?php

namespace Kjdion84\Turtle\Models;

use Illuminate\Database\Eloquent\Model;
use Kjdion84\Turtle\Traits\InTime;

class Permission extends Model
{
    use InTime;

    protected $fillable = ['group', 'name'];

    // create group
    public function createGroup($group, $names = [])
    {
        foreach ($names as $name) {
            $this->create([
                'group' => $group,
                'name' => $name,
            ]);
        }
    }

    // roles relationship
    public function roles()
    {
        return $this->belongsToMany(config('turtle.models.role'));
    }

    // users relationship
    public function users()
    {
        return $this->belongsToMany(config('turtle.models.user'));
    }
}