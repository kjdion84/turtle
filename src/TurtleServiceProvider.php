<?php

namespace Kjdion84\Turtle;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use ReCaptcha\ReCaptcha;

class TurtleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // config
        $this->publishes([__DIR__ . '/../config/turtle.php' => config_path('turtle.php')], 'required');

        // routes
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        // migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'turtle');
        $this->publishes([__DIR__ . '/../resources/views/layouts/app.blade.php' => resource_path('views/vendor/turtle/layouts/app.blade.php')], 'required');
        $this->publishes([__DIR__ . '/../resources/views' => resource_path('views/vendor/turtle')], 'views');

        // assets
        $this->publishes([__DIR__ . '/../public' => public_path('turtle')], 'required');
        $this->publishes([__DIR__ . '/../public' => public_path('turtle')], 'public');

        // allow middleware
        $this->app['router']->aliasMiddleware('allow', 'Kjdion84\Turtle\Middleware\Allow');

        // gate permissions
        Gate::before(function ($user, $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        });

        // validator extensions
        $this->validatorExtensions();

        // blade directives
        $this->bladeDirectives();

        // crud command
        if ($this->app->runningInConsole()) {
            $this->commands([Commands\CrudCommand::class]);
        }

        // crud resources
        $this->publishes([__DIR__ . '/../resources/crud/UsedCar.php' => resource_path('crud/UsedCar.php')], 'crud_example');
        $this->publishes([__DIR__ . '/../resources/crud/stubs' => resource_path('crud/stubs/default')], 'crud_stubs');
    }

    public function register()
    {
        // merge config
        $this->mergeConfigFrom(__DIR__ . '/../config/turtle.php', 'turtle');
    }

    public function validatorExtensions()
    {
        // recaptcha rule
        Validator::extend('recaptcha', function ($attribute, $value, $parameters, $validator) {
            $recaptcha = new ReCaptcha(config('turtle.recaptcha.secret_key'));
            $resp = $recaptcha->verify($value, request()->ip());

            return $resp->isSuccess();
        }, 'The reCAPTCHA response is invalid.');
    }

    public function bladeDirectives()
    {
        // canany directive
        Blade::directive('canany', function ($permissions) {
            $permissions = array_map('trim', explode(',', $permissions));
            $conditional = [];

            foreach ($permissions as $permission) {
                $conditional[] = 'Gate::check(' . $permission . ')';
            }

            return '<?php if (' . implode(' || ', $conditional) . '): ?>';
        });
        Blade::directive('endcanany', function () {
            return '<?php endif; ?>';
        });
    }
}