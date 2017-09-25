<?php

return [

    // do not add trailing slashes!
    'paths' => [
        'stubs' => 'vendor/kjdion84/turtle/resources/bread/stubs',
        'controller' => 'app/Http/Controllers',
        'model' => 'app',
        'views' => 'resources/views',
        'navbar' => 'resources/views/vendor/turtle/layouts/app.blade.php',
        'routes' => 'routes/web.php',
    ],

    // model attribute definitions
    'attributes' => [
        'title' => [
            'schema' => 'string("bread_attribute_name")->unique()',
            'input' => 'text',
            'rule_add' => 'required|unique:bread_model_variables',
            'rule_edit' => 'required|unique:bread_model_variables,bread_attribute_name,$id',
            'datatable' => true,
        ],
        'make' => [
            'schema' => 'string("bread_attribute_name")',
            'input' => 'text',
            'rule_add' => 'required',
            'rule_edit' => 'required',
            'datatable' => true,
        ],
        'model' => [
            'schema' => 'string("bread_attribute_name")',
            'input' => 'text',
            'rule_add' => 'required',
            'rule_edit' => 'required',
            'datatable' => true,
        ],
        'year' => [
            'schema' => 'integer("bread_attribute_name")',
            'input' => 'number',
            'rule_add' => 'required|numeric',
            'rule_edit' => 'required|numeric',
            'datatable' => true,
        ],
        'description' => [
            'schema' => 'text("bread_attribute_name")->nullable()',
            'input' => 'textarea',
        ],
    ],

];