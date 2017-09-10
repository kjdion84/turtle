![Imgur](https://i.imgur.com/REzZP08.png)

# Turtle

Turtle is a Laravel 5.5 package with front & backend scaffolding including a CRUD generator, auth integration, roles, permissions, contact forms, reCAPTCHA, activity logs, demo mode, user timezones, AJAX CRUD/validation, Bootstrap 4, DataTables, & more!

## Useful Links

* Repo: https://github.com/kjdion84/turtle
* Demo: http://turtledemo.kjdion.com (admin@example.com/admin123)

# Installation

## Require via Composer

```
composer require kjdion84/turtle:"~1.0"
```

## Publish Required Files

```
php artisan vendor:publish --provider="Kjdion84\Turtle\TurtleServiceProvider" --tag="required"
```

This will create the following files:

```
config/turtle.php
resources/views/kjdion84/turtle/layouts/app.blade.php
public/kjdion84/turtle/*.*
```

## Modify Existing Files

Add the `LikesPizza` trait to `App\User` e.g.:

```
use Notifiable, LikesPizza;
```

Add `timezone` to the `App\User` fillables e.g.:

```
protected $fillable = [
    'name', 'email', 'password', 'timezone',
];
```

Add the `Shellshock` trait to `App\Http\Controllers\Controller` e.g.:

```
use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Shellshock;
```

**(Recommended but optional)** uncomment `AuthenticateSession` inside of `App\Http\Kernel` e.g.:

```
\Illuminate\Session\Middleware\AuthenticateSession::class,
```

## Config & Migrate

Make sure your database and SMTP server is configured in your `.env` file, then migrate:

```
php artisan migrate
```

If you get a string length error, add `Schema::defaultStringLength(191)` to the boot method of `App\Providers\AppServiceProvider` e.g.:

```
public function boot()
{
    Schema::defaultStringLength(191);
}
```

## Remove Default `/` & Auth Routes

Comment out or completely remove the default `/` route inside of `routes/web.php` e.g.:

```
/*
Route::get('/', function () {
    return view('welcome');
});
*/
```

You must also comment out or remove any of the default Laravel auth routes if they are present in `routes/web.php` e.g.:

```
// Auth::routes();
```

To avoid any other routing conflicts, check out `vendor/kjdion84/turtle/src/routes.php` to ensure that your existing routes do not have the same URL or names.

## Logging In

Now that installation is done, you can visit your app URL and log in using `admin@example.com` and `admin123` as the password. I recommend changing these credentials right away!

## Optional Cleanup

You can remove the `app\Http\Controllers\Auth` folder and the `resources/views/welcome.blade.php` file if you want. They are no longer needed.

## Optional Publishing

Need a bit more control? No problem.

You can publish all of the migrations to `database/migrations/*.*` with:

```
php artisan vendor:publish --provider="Kjdion84\Turtle\TurtleServiceProvider" --tag="migrations"
```

You can publish all of the views to `resources/views/kjdion84/turtle/*.*` with:

```
php artisan vendor:publish --provider="Kjdion84\Turtle\TurtleServiceProvider" --tag="views"
```

# Updating

Since composer does not allow `scripts` in package `composer.json` files, you must run this to update the public assets:

```
php artisan vendor:publish --provider="Kjdion84\Turtle\TurtleServiceProvider" --tag="public" --force
```

Or add that command to `post-update-cmd` under `scripts` in your Laravel root `composer.json` file.

# Configuration

You can enable/disable the core features inside of `config/turtle.php`:

* `allow.frontend`: enable/disable the frontend
* `allow.registration`: enable/disable user registration
* `allow.contact`: enable/disable the contact form
* `demo_mode`: enable/disable demo mode (only allows login, but still shows buttons & features)
* `recaptcha.site_key`: your reCAPTCHA site key (optional)
* `recaptcha.secret_key`: your reCAPTCHA secret key (optional)
* `classes.*.*`: change these if you want the package to use your own classes

## Using Your Own Classes

You can easily just extend the package models & controllers if you need more control.

For example, you're probably going to want to change the `dashboard()` method in `AppController` to show charts or something. So you'd create your new controller file inside `App\Controllers` and extend the turtle `AppController` class.

Then you can simply override the `dashboard()` method to do whatever you want. This can be done for every single model & controller of the package. Check out the model & controller files in `vendor/kjdion84/turtle/src` to see the methods you can override and what they do by default.

## reCAPTCHA

You must enter your reCAPTCHA keys in order for reCAPTCHA to display in the register/contact forms. If no reCAPTCHA keys are entered, those forms simple won't use it which leaves you vulnerable to spam & bot accounts.

# Usage

## Helpers

### `flash($class, $message)`

Flashes a message to the session which will display on the next request via a Bootstrap 4 alert.

* `$class`: the Bootstrap 4 `alert-` class to use e.g. `success`
* `$message`: the message to display in the alert e.g. `User updated!`

### `activity($log, $model = null)`

Logs a new activity in the database via the `Activity` model.

* `$log`: the message to log e.g. `Updated User`
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

The package controller methods return a JSON response for CRUD operations. This is due to the form validation AJAX. Each JSON key you return has a specific function:

* `redirect`: redirects user to specified URL e.g. `'redirect' => route('index')`
* `flash`: flashes alert briefly using bs4 class e.g. `'flash' => ['success', 'User created!']`
* `dismiss_modal`: closes the current model the form is in
* `reload_datatables`: reloads datatables on the page to display new/updated data

## CRUD Command

Use `php artisan make:crud {file}` to generate CRUD files e.g.:

```
php artisan make:crud resources/crud/MyModel.php
```

This will generate a controller, model, migration, views, add a navbar menu item, and routes.

You must make sure you create a `resources/crud/MyModel.php` file before running the command, where `MyModel` is the name of the model you want to generate. This model file will contain all of the path & attribute definitions for the model. Check out `vendor/kjdion84/turtle/resources/crud/UsedCar.php` for an example, or publish the example using:

```
php artisan vendor:publish --provider="Kjdion84\Turtle\TurtleServiceProvider" --tag="crud_example"
```

This will create `resources/crud/UsedCar.php`.

### Model Path & Attribute Definitions

The CRUD command requires you to specify model paths & attributes via a PHP file.

#### Paths

Use the paths array to define exactly which paths you want the generator to use for the model:

* `stubs`: the stub template folder to be used when generating e.g. `resources/crud/stubs/mytemplate`
* `controller`: the folder used for the generated controller e.g. `app/Http/Controllers`
* `model`: the folder used for the generated model e.g. `app`
* `views`: the folder used for the generated views e.g. `resources/views`
* `navbar`: the file containing the `<!-- crud_navbar -->` hook which the menu item is placed under e.g. `resources/views/kjdion84/turtle/layouts/app.blade.php`
* `routes`: the file which generated routes will be appended to e.g. `routes/web.php`

#### Attributes

Attributes are specified in a key value pair, where the key is the name of the attribute and the value is its options. The following options are available per attribute:

* `schema`: methods used for the migration column e.g. `string("crud_attribute_name")->nullable()`
* `input`: input type for forms which can be `text`, `password`, `email`, `number`, `tel`, `url`, `radio`, `checkbox`, `select`, or `textarea`
* `rule_create`: rules used for creating by the controller e.g. `required|unique:crud_model_variables`
* `rule_update`: rules used for updating by the controller e.g. `required|unique:crud_model_variables,crud_attribute_name,$id` (note `$id`, this is a variable injected into the controller method)
* `datatable`: enable/disable showing this attribute in DataTables (boolean)

You can also completely remove any option you do not want to use per attribute.

#### Replacement Strings

There are a number of replacement strings you will see in the stub template files and even the `UsedCar.php` example file:

* `crud_attribute_name`: current attribute name e.g. `post_title`
* `crud_attribute_label`: current attribute label (automatically created using the attribute name) e.g. `Post Title`
* `crud_attribute_schema`: current attribute schema e.g. `string("crud_attribute_name")->nullable()`
* `crud_attribute_input`: current attribute input e.g. `textarea`
* `crud_attribute_rule_create`: current attribute create rule e.g. `required|unique:crud_model_variables`
* `crud_attribute_rule_update`: current attribute update rule e.g. `required|unique:crud_model_variables,crud_attribute_name,$id`
* `crud_attribute_datatable`: show this attribute in datatables? boolean value e.g. `true`
* `crud_model_class`: model class name e.g. `BlogPost`
* `crud_model_variables`: plural model variable name e.g. `blog_posts`
* `crud_model_variable`: singular model variable name e.g. `blog_post`
* `crud_model_strings`: plural model title name e.g. `Blog Posts`
* `crud_model_string`: singular model title name e.g. `Blog Post`
* `/* crud_model_namespace */`: model namespace line e.g. `namespace App\BlogPost;`
* `/* crud_model_use */`: model use line e.g. `use App\BlogPost;`
* `crud_controller_class`: controller class name e.g. `BlogPostController`
* `crud_controller_view`: view path used by controller methods e.g. `blog_posts.`
* `crud_controller_routes`: controller path for routes e.g. `Backend\BlogPostController`
* `/* crud_controller_namespace */`: controller namespace line e.g. `namespace App\Http\Controllers;`

You can use any of these replacement strings inside of the stub templates or model attribute definition files you create.

### Custom Stub Templates

You can easily publish the default stub folder to `resources/crud/stubs/default` with:

```
php artisan vendor:publish --provider="Kjdion84\Turtle\TurtleServiceProvider" --tag="crud_stubs"
```

After doing so, simply rename the folder `default` to whatever you want. Now you can modify it to your hearts desires. Just make sure you specify the full path to this new folder in the `paths.stubs` value for any CRUD model file you want to use it.

# Issues & Support

Use Github issues for bug reports, suggestions, help, & support.