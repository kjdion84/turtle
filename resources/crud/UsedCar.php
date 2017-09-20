<?php

return [

    // do not add trailing slashes!
    'paths' => [
        'stubs' => 'vendor/kjdion84/turtle/resources/crud/stubs',
        'controller' => 'app/Http/Controllers',
        'model' => 'app',
        'views' => 'resources/views',
        'navbar' => 'resources/views/vendor/turtle/layouts/app.blade.php',
        'routes' => 'routes/web.php',
    ],

    // model attribute definitions
    'attributes' => [
        'title' => [
            'schema' => 'string("crud_attribute_name")->unique()',
            'input' => 'text',
            'rule_create' => 'required|unique:crud_model_variables',
            'rule_update' => 'required|unique:crud_model_variables,crud_attribute_name,$id',
            'datatable' => true,
        ],
        'make' => [
            'schema' => 'string("crud_attribute_name")',
            'input' => 'text',
            'rule_create' => 'required',
            'rule_update' => 'required',
            'datatable' => true,
        ],
        'model' => [
            'schema' => 'string("crud_attribute_name")',
            'input' => 'text',
            'rule_create' => 'required',
            'rule_update' => 'required',
            'datatable' => true,
        ],
        'year' => [
            'schema' => 'integer("crud_attribute_name")',
            'input' => 'number',
            'rule_create' => 'required|numeric',
            'rule_update' => 'required|numeric',
            'datatable' => true,
        ],
        'description' => [
            'schema' => 'text("crud_attribute_name")->nullable()',
            'input' => 'textarea',
        ],
    ],

];