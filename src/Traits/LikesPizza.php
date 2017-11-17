<?php

namespace Kjdion84\Turtle\Traits;

trait LikesPizza
{
    // roles relationship
    public function roles()
    {
        return $this->belongsToMany(config('turtle.models.role'));
    }

    // activities relationship
    public function activities()
    {
        return $this->hasMany(config('turtle.models.activity'));
    }

    // gate permissions
    public function hasPermission($name)
    {
        // admin role always has permission
        if ($this->roles->contains('name', 'Admin')) {
            return true;
        }

        // user permissions are role-based
        $permission = app(config('turtle.models.permission'))->where('name', $name)->first();

        if ($permission) {
            return $permission->roles->intersect($this->roles)->count() > 0;
        }

        return false;
    }
}