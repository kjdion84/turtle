![Imgur](https://i.imgur.com/REzZP08.png)

# Turtle

Turtle is a Laravel 5.5 package with front & backend scaffolding including a BREAD generator, auth integration, Stripe billing, roles, permissions, contact forms, reCAPTCHA, activity logs, demo mode, user timezones, AJAX BREAD/validation, Bootstrap 4, DataTables, & more!

## Useful Links

* Repo: https://github.com/kjdion84/turtle
* Demo: http://turtledemo.kjdion.com
    * Admin Login: admin@example.com/admin123
    * Tenant Login: tester@example.com/test123

## Readme Navigation

* [Installation](#installation)
* [Configuration](#configuration)
* [Billing](#billing)
* [Usage](#usage)
* [Issues & Support](#issues--support)

# Installation

## Require via Composer

```
composer require kjdion84/turtle:"~1.4"
```

## Publish Required Files

```
php artisan vendor:publish --provider="Kjdion84\Turtle\TurtleServiceProvider" --tag="required"
```

This will create the following files:

```
config/turtle.php
resources/views/vendor/turtle/layouts/app.blade.php
public/turtle/*.*
```

## Auto-Publish Public Assets After Updates

Add the publish command for the `public` tag to your project `composer.json` `scripts` e.g.:

```
"scripts": {
    "post-update-cmd": [
        "php artisan vendor:publish --provider=Kjdion84\\Turtle\\TurtleServiceProvider --tag=public --force"
    ]
}
```

## Modify Existing Files

Import & add the `InTime` and `LikesPizza` traits to your Auth `User` (normally `App\User`) model e.g.:

```
use Notifiable, InTime, LikesPizza;
```

Add the `timezone` fillable to your Auth `User` model e.g.:

```
protected $fillable = [
    'name', 'email', 'password', 'timezone',
];
```

Uncomment `AuthenticateSession` inside of `App\Http\Kernel` e.g.:

```
\Illuminate\Session\Middleware\AuthenticateSession::class,
```

Add expired session handling to the `App\Exceptions\Handler` `render()` method e.g.:

```
public function render($request, Exception $exception)
{
    if ($exception instanceof TokenMismatchException) {
        flash('danger', 'Session expired, please try again.');

        return response()->json(['reload_page' => true]);
    }

    return parent::render($request, $exception);
}
```

## Config & Migrate

Set your MySQL engine to InnoDB inside of `config/database.php` e.g.:

```
'engine' => 'InnoDB ROW_FORMAT=DYNAMIC',
```

Make sure your database and SMTP server is configured in your `.env` file, then migrate:

```
php artisan migrate
```

## Remove Default `/` & Auth Routes

Comment out or completely remove the default `/` and `Auth` routes inside of `routes/web.php` e.g.:

```
/*
Route::get('/', function () {
    return view('welcome');
});
*/

// Auth::routes();
```

## Logging In

Now that installation is done, you can visit your app URL and log in using `admin@example.com` and `admin123` as the password. I recommend changing these credentials right away!

## Optional Cleanup

You can remove the `app\Http\Controllers\Auth` folder and the `resources/views/welcome.blade.php` file if you want. They are no longer needed.

## Optional Publishing

You can publish all of the views to `resources/views/vendor/turtle/*.*` with:

```
php artisan vendor:publish --provider="Kjdion84\Turtle\TurtleServiceProvider" --tag="views"
```

# Configuration

You can enable/disable the core features inside of `config/turtle.php`:

* `allow.frontend`: enable/disable the frontend
* `allow.registration`: enable/disable user registration
* `allow.contact`: enable/disable the contact form
* `allow.billing`: enable/disable user billing
* `billing.*`: configuration for billing & stripe integration (see [Billing](#billing))
* `demo_mode`: enable/disable demo mode (only allows login, but still shows buttons & features)
* `recaptcha.site_key`: your reCAPTCHA site key (optional)
* `recaptcha.secret_key`: your reCAPTCHA secret key (optional)
* `classes.*`: change these if you want the package to use your own classes

## reCAPTCHA

You must enter your reCAPTCHA keys in order for reCAPTCHA to display in the register/contact forms. If no reCAPTCHA keys are entered, those forms simple won't use it which leaves you vulnerable to spam & bot accounts.

## Using Custom Classes

You can easily just extend the package models & controllers if you need more control.

For example, you're probably going to want to change the `dashboard()` method in `AppController` to show charts or something. So you'd create your new controller file inside `App\Controllers` and extend the turtle `AppController` class.

Then you can simply override the `dashboard()` method to do whatever you want. This can be done for every single model & controller of the package. Check out the model & controller files in `vendor/kjdion84/turtle/src` to see the methods you can override and what they do by default.

Also, make sure to update `config/turtle.php` with the class namespace for the new controller.

# Billing

Turtle comes with user billing capabilities built in, which is fully integrated with the Stripe API. If you wish to enable billing and plan limits, please read the following directions.

First, set up your Stripe account stuff:

* Make sure you [verify your phone number](https://dashboard.stripe.com/phone-verification)
* Add all of your [subscription plans](https://dashboard.stripe.com/plans)
* [Create a webhook](https://dashboard.stripe.com/account/webhooks) for `invoice.payment_suceeded` pointing to the `billing/webhook` route

Add the `billing_*` fillables to your Auth `User` model e.g.:

```
protected $fillable = [
    'name', 'email', 'password', 'timezone',
    'billable', 'billing_customer', 'billing_subscription', 'billing_plan', 'billing_cc_last4', 'billing_trial_ends', 'billing_period_ends',
];
```

Add your Stripe API key & plan information to the `turtle.billing` config values e.g.:

```
'billing' => [
    'stripe_secret_key' => 'sk_test_QSlbZsEyVA0pRaqyWKduKRYT',
    'trial' => [
        'period' => '30 days',
        'limits' => [
            // model => limit (null = unlimited)
            'App\Post' => 1,
        ],
    ],
    'plans' => [
        // Stripe plan ID => [options]
        'Basic' => [
            'html' => '
                <p>A great starter plan for individuals.</p>
                <ul class="list-unstyled">
                    <li><b>5</b> Posts</li>
                </ul>
                <p class="lead"><b>$10</b>/month</p>
            ',
            'limits' => [
                'App\Post' => 5,
            ],
        ],
        'Plus' => [
            'html' => '
                <p>A bit more flexible, ideal for teams.</p>
                <ul class="list-unstyled">
                    <li><b>25</b> Posts</li>
                </ul>
                <p class="lead"><b>$20</b>/month</p>
            ',
            'limits' => [
                'App\Post' => 25,
            ],
        ],
        'Premium' => [
            'html' => '
                <p>Fully unlimited! Perfect for companies.</p>
                <ul class="list-unstyled">
                    <li><b>Unlimited</b> Posts</li>
                </ul>
                <p class="lead"><b>$50</b>/month</p>
            ',
            'limits' => [
                'App\Post' => null,
            ],
        ],
    ],
],
```

Disable CSRF protection for the stripe webhook route in `App\Http\Middleware\VerifyCsrfToken` e.g.:

```
protected $except = [
    'billing/webhook',
];
```

When generating BREAD for any tenant-related models (e.g. `App\Post` in the above example), use the `tenant` stubs path e.g.:

```
'stubs' => 'vendor/kjdion84/turtle/resources/bread/stubs/tenant',
```

This will make sure the model uses the `UserScope`, has a `user_id` fillable & database column, does not require permissions for owned models, and has the `shellshock()` parameter set for plan limiting (which shows an error alert if they have reached their limit when attempting to add).

Billable users will have a `Billing` dropdown option when they click on their username in the navbar as well. They will also be shown an alert if they are in free trial mode or their account has become inactive due to lack of payment. Also, there will be a checkbox for `Billable` in the `User` add/edit modals, which allows you enable or disable billing per user (for example, if you want to give a specific user free access to your app forever, uncheck this box).

# Usage

## Helpers

### `flash($class, $message)`

Flashes a message to the session which will display on the next request via a Bootstrap 4 alert.

* `$class`: the Bootstrap 4 `alert-` class to use e.g. `success`
* `$message`: the message to display in the alert e.g. `User edited!`

### `activity($log, $model = null)`

Logs a new activity in the database via the `Activity` model.

* `$log`: the message to log e.g. `Edited User`
* `$model`: the model the activity is being performed on e.g. `App\User`

`$model` is optional. Also, this helper function automatically saves request input data, with the exception of `_method`, `_token`, `current_password`, `password`, `password_confirmation`, and `g-recaptcha-response`.

### `timezones()`

This function will return a nicely named and organized list of PHP timezones along with their  UTC offsets. It uses the `timezone_identifiers_list()` function, so DST correction is not an issue.

## Traits

### InTime

This trait will convert (via accessors) the model `created_at`, `updated_at`, and `deleted_at` attributes to the users specified timezone.

### LikesPizza

Contains the role, permission, & activity relationships and functions for auth users.

### Shellshock

This is similar to Laravels `validate()` method, but it will totally stop an action from occurring if demo mode is enabled. You **must** use `shellshock()` in all of your controllers methods for validation if you are going to enable demo mode to show your app to people.

### Responses

The package controller methods return a JSON response for BREAD operations. This is due to the form validation AJAX. Each JSON key you return has a specific function:

* `redirect`: redirects user to specified URL e.g. `'redirect' => route('index')`
* `flash`: flashes alert briefly using bs4 class e.g. `'flash' => ['success', 'User added!']`
* `dismiss_modal`: closes the current model the form is in
* `reload_page`: reloads the current location
* `reload_datatables`: reloads datatables on the page to display new/updated data
* `reload_target`: reloads target element using the specified selector and `reload_target_data` value e.g. `'reload_target' => '#my_target', 'reload_target_data' => view('my_view')->render()`
* `reload_sources`: reloads the content of each `data-reload-source` element using the URL specified e.g. `<div data-reload-source="{{ route('my_source') }}"></div>`

## BREAD Command

Use `php artisan make:bread {file}` to generate BREAD files e.g.:

```
php artisan make:bread resources/bread/MyModel.php
```

This will generate a controller, model, migration, views, add a navbar menu item, and routes.

You must make sure you create a `resources/bread/MyModel.php` file before running the command, where `MyModel` is the name of the model you want to generate. This model file will contain all of the path & attribute definitions for the model. Check out `vendor/kjdion84/turtle/resources/bread/UsedCar.php` for an example, or publish the example using:

```
php artisan vendor:publish --provider="Kjdion84\Turtle\TurtleServiceProvider" --tag="bread_example"
```

This will create `resources/bread/UsedCar.php`.

### Model Path & Attribute Definitions

The BREAD command requires you to specify model paths & attributes via a PHP file.

#### Paths

Use the paths array to define exactly which paths you want the generator to use for the model:

* `stubs`: the stub template folder to be used when generating e.g. `resources/bread/stubs/mytemplate`
* `controller`: the folder used for the generated controller e.g. `app/Http/Controllers`
* `model`: the folder used for the generated model e.g. `app`
* `views`: the folder used for the generated views e.g. `resources/views`
* `navbar`: the file containing the `<!-- bread_navbar -->` hook which the menu item is placed under e.g. `resources/views/vendor/turtle/layouts/app.blade.php`
* `routes`: the file which generated routes will be appended to e.g. `routes/web.php`

#### Attributes

Attributes are specified in a key value pair, where the key is the name of the attribute and the value is its options. The following options are available per attribute:

* `schema`: methods used for the migration column e.g. `string("bread_attribute_name")->nullable()`
* `input`: input type for forms which can be `text`, `password`, `email`, `number`, `tel`, `url`, `radio`, `checkbox`, `select`, or `textarea`
* `rule_add`: rules used for creating by the controller e.g. `required|unique:bread_model_variables`
* `rule_edit`: rules used for updating by the controller e.g. `required|unique:bread_model_variables,bread_attribute_name,$id` (note `$id`, this is a variable injected into the controller method)
* `datatable`: enable/disable showing this attribute in DataTables (boolean)

You can also completely remove any option you do not want to use per attribute.

#### Replacement Strings

There are a number of replacement strings you will see in the stub template files and even the `UsedCar.php` example file:

* `bread_attribute_name`: current attribute name e.g. `post_title`
* `bread_attribute_label`: current attribute label (automatically created using the attribute name) e.g. `Post Title`
* `bread_attribute_schema`: current attribute schema e.g. `string("bread_attribute_name")->nullable()`
* `bread_attribute_input`: current attribute input e.g. `textarea`
* `bread_attribute_rule_add`: current attribute create rule e.g. `required|unique:bread_model_variables`
* `bread_attribute_rule_edit`: current attribute update rule e.g. `required|unique:bread_model_variables,bread_attribute_name,$id`
* `bread_attribute_datatable`: show this attribute in datatables? boolean value e.g. `true`
* `bread_model_class`: model class name e.g. `BlogPost`
* `bread_model_class_full`: model class with namespace e.g. `App\BlogPost`
* `bread_model_variables`: plural model variable name e.g. `blog_posts`
* `bread_model_variable`: singular model variable name e.g. `blog_post`
* `bread_model_strings`: plural model title name e.g. `Blog Posts`
* `bread_model_string`: singular model title name e.g. `Blog Post`
* `/* bread_model_namespace */`: model namespace line e.g. `namespace App\BlogPost;`
* `/* bread_model_use */`: model use line e.g. `use App\BlogPost;`
* `bread_controller_class`: controller class name e.g. `BlogPostController`
* `bread_controller_view`: view path used by controller methods e.g. `blog_posts.`
* `bread_controller_routes`: controller path for routes e.g. `Backend\BlogPostController`
* `/* bread_controller_namespace */`: controller namespace line e.g. `namespace App\Http\Controllers;`

You can use any of these replacement strings inside of the stub templates or model attribute definition files you create.

### Custom Stub Templates

You can easily publish the default stub folder to `resources/bread/stubs/default` with:

```
php artisan vendor:publish --provider="Kjdion84\Turtle\TurtleServiceProvider" --tag="bread_stubs"
```

After doing so, simply rename the folder `default` to whatever you want. Now you can modify it to your hearts desires. Just make sure you specify the full path to this new folder in the `paths.stubs` value for any BREAD model file you want to use it.

# Issues & Support

Use Github issues for bug reports, suggestions, help, & support.