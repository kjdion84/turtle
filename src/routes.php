<?php

$aRouteGroupConfig = ['middleware' => 'web'];
$bHasPrefix = false;

if (config('turtle.routes.prefix') !== null)
{
    $bHasPrefix = true;
    $aRouteGroupConfig['prefix'] = config('turtle.routes.prefix');
}

Route::group($aRouteGroupConfig, function () use ($bHasPrefix){

    // app routes
    if ($bHasPrefix)
    {
        Route::get('/index', config('turtle.controllers.app') . '@index')->name('index');
        Route::get('home', config('turtle.controllers.app') . '@indexRedirect');
        Route::get('/', config('turtle.controllers.app') . '@dashboard')->name('dashboard');
    }
    else
    {
        Route::get('/', config('turtle.controllers.app') . '@index')->name('index');
        Route::get('home', config('turtle.controllers.app') . '@indexRedirect');
        Route::get('dashboard', config('turtle.controllers.app') . '@dashboard')->name('dashboard');
    }
    
    Route::get('delete/{route}/{id}', config('turtle.controllers.app') . '@deleteModal')->name('delete');
    Route::get('contact', config('turtle.controllers.app') . '@contactForm')->name('contact');
    Route::post('contact', config('turtle.controllers.app') . '@contact');

    // auth routes
    Route::get('login', config('turtle.controllers.auth') . '@loginForm')->name('login');
    Route::post('login', config('turtle.controllers.auth') . '@login');
    Route::get('logout', config('turtle.controllers.auth') . '@logout')->name('logout');
    Route::get('register', config('turtle.controllers.auth') . '@registerForm')->name('register');
    Route::post('register', config('turtle.controllers.auth') . '@register');
    Route::get('profile', config('turtle.controllers.auth') . '@profileForm')->name('profile');
    Route::patch('profile', config('turtle.controllers.auth') . '@profile');
    Route::get('password/email', config('turtle.controllers.auth') . '@passwordEmailForm')->name('password.email');
    Route::post('password/email', config('turtle.controllers.auth') . '@passwordEmail');
    Route::get('password/reset/{token?}', config('turtle.controllers.auth') . '@passwordResetForm')->name('password.reset');
    Route::post('password/reset', config('turtle.controllers.auth') . '@passwordReset');
    Route::get('password/change', config('turtle.controllers.auth') . '@passwordChangeForm')->name('password.change');
    Route::patch('password/change', config('turtle.controllers.auth') . '@passwordChange');

    // role routes
    Route::get('roles', config('turtle.controllers.role') . '@index')->name('roles');
    Route::get('roles/datatable', config('turtle.controllers.role') . '@indexDatatable')->name('roles.datatable');
    Route::get('roles/add', config('turtle.controllers.role') . '@addModal')->name('roles.add');
    Route::post('roles/add', config('turtle.controllers.role') . '@add');
    Route::get('roles/edit/{id}', config('turtle.controllers.role') . '@editModal')->name('roles.edit');
    Route::patch('roles/edit/{id}', config('turtle.controllers.role') . '@edit');
    Route::delete('roles/delete', config('turtle.controllers.role') . '@delete')->name('roles.delete');

    // user routes
    Route::get('users', config('turtle.controllers.user') . '@index')->name('users');
    Route::get('users/datatable', config('turtle.controllers.user') . '@indexDatatable')->name('users.datatable');
    Route::get('users/add', config('turtle.controllers.user') . '@addModal')->name('users.add');
    Route::post('users/add', config('turtle.controllers.user') . '@add');
    Route::get('users/edit/{id}', config('turtle.controllers.user') . '@editModal')->name('users.edit');
    Route::patch('users/edit/{id}', config('turtle.controllers.user') . '@edit');
    Route::get('users/password/{id}', config('turtle.controllers.user') . '@passwordModal')->name('users.password');
    Route::patch('users/password/{id}', config('turtle.controllers.user') . '@password');
    Route::delete('users/delete', config('turtle.controllers.user') . '@delete')->name('users.delete');
    Route::get('users/activity/{id}', config('turtle.controllers.user') . '@activity')->name('users.activity');
    Route::get('users/activity/datatable/{id}', config('turtle.controllers.user') . '@activityDatatable')->name('users.activity.datatable');
    Route::get('users/activity/data/{id}', config('turtle.controllers.user') . '@activityDataModal')->name('users.activity.data');
});