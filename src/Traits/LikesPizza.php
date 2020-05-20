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

    // permissions relationship
    public function permissions()
    {
        return $this->belongsToMany(config('turtle.models.permission'));
    }

    // gate permissions
    public function hasPermission($name)
    {
        // admin role always has permission
        if ($this->roles->contains('name', 'Admin')) {
            return true;
        }

        // permission by user
        if ($this->permissions->contains('name', $name)){
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