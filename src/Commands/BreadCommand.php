<?php

namespace Kjdion84\Turtle\Commands;

use DirectoryIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class BreadCommand extends Command
{
    protected $signature = 'make:bread {file}'; // php artisan make:bread resources/bread/UsedCar.php
    protected $description = 'Generate BREAD files.';
    public $options = [];
    public $replace = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if (file_exists($this->argument('file'))) {
            // set options and generate
            $this->options = include $this->argument('file');
            $this->setReplaceModel()->setReplaceAttributes()->generate();

            // ask to migrate
            if ($this->ask('Migrate now? [y/n]') == 'y') {
                Artisan::call('migrate');
                $this->info('Migration complete!');
            }

            // output success message
            $this->info($this->replace['model']['bread_model_class'] . ' BREAD generated!');
        }
        else {
            // file does not exist, show error
            $this->error('Error: ' . $this->argument('file') . ' does not exist.');
        }
    }

    public function setReplaceModel()
    {
        $model = basename($this->argument('file'), '.php');
        $controller = $model.'Controller';
        $string = trim(preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $model));

        $this->replace['model'] = [
            'bread_model_class' => $model,
            'bread_model_variables' => str_replace(' ', '_', strtolower(str_plural($string))),
            'bread_model_variable' => str_replace(' ', '_', strtolower($string)),
            'bread_model_strings' => str_plural($string),
            'bread_model_string' => $string,
            '/* bread_model_namespace */' => 'namespace ' . $this->replaceNamespace($this->options['paths']['model']) . ';',
            '/* bread_model_use */' => 'use '. $this->replaceNamespace($this->options['paths']['model']) . '\\' . $model . ';',
            'bread_controller_class' => $controller,
            'bread_controller_view' => $this->replaceView($this->options['paths']['views']),
            'bread_controller_routes' => ltrim(str_replace('App\\Http\\Controllers', '', $this->replaceNamespace($this->options['paths']['controller'])) . '\\' . $controller, '\\'),
            '/* bread_controller_namespace */' => 'namespace ' . $this->replaceNamespace($this->options['paths']['controller']) . ';',
        ];

        return $this;
    }

    public function replaceNamespace($path)
    {
        $namespace = str_replace('app', 'App', $path);
        $namespace = str_replace('/', '\\', $namespace);

        return $namespace;
    }

    public function replaceView($path)
    {
        $view = str_replace('resources/views', '', $path);
        $view = str_replace('/', '.', $view) . '.';

        return ltrim($view, '.');
    }

    public function setReplaceAttributes()
    {
        $replace = [];

        foreach ($this->options['attributes'] as $name => $options) {
            // schema
            if (isset($options['schema'])) {
                $replace['/* bread_schema */'][] = $this->replaceAttribute('database/schema.php', $name, $options);
            }

            // input
            if (isset($options['input'])) {
                foreach (['add', 'edit'] as $action) {
                    $replace['<!-- bread_input_' . $action . ' -->'][] = $this->replaceAttribute('views/input/' . $action . '/' . $this->replaceInput($options) . '.blade.php', $name, $options);
                }
            }

            // rule
            foreach (['add', 'edit'] as $action) {
                if (isset($options['rule_' . $action])) {
                    $replace['/* bread_rule_' . $action . ' */'][] = $this->replaceAttribute('controller/rule/' . $action . '.php', $name, $options);
                }
            }

            // datatable
            if (isset($options['datatable']) && $options['datatable']) {
                $replace['<!-- bread_datatable_heading -->'][] = $this->replaceAttribute('views/datatable/heading.blade.php', $name, $options);
                $replace['/* bread_datatable_column */'][] = $this->replaceAttribute('views/datatable/column.blade.php', $name, $options);
            }
        }

        $replace['/* bread_fillable */'] = 'protected $fillable = ["' . implode('", "', array_keys($this->options['attributes'])) . '"];';

        foreach ($replace as $key => $values) {
            $this->replace['attributes'][$key] = trim(is_array($values) ? implode(PHP_EOL, $values) : $values);
        }

        return $this;
    }

    public function replaceAttribute($file, $name, $options)
    {
        $file = base_path($this->options['paths']['stubs']) . '/' . $file;

        if (file_exists($file)) {
            $content = file_get_contents($file);

            foreach ($options as $key => $value) {
                $content = str_replace('bread_attribute_' . $key, $value, $content);
            }

            $content = str_replace('bread_attribute_label', ucwords(str_replace('_', ' ', $name)), $content);
            $content = str_replace('bread_attribute_name', $name, $content);
        }

        return isset($content) ? $content : null;
    }

    public function replaceInput($options)
    {
        $input = isset($options['input']) ? $options['input'] : null;

        if (in_array($input, ['text', 'password', 'email', 'number', 'tel', 'url'])) {
            $input = 'input';
        }
        else if (in_array($input, ['radio', 'checkbox'])) {
            $input = 'check';
        }

        return $input;
    }

    public function generate()
    {
        // create controller file
        if (!file_exists(base_path($this->options['paths']['controller']))) mkdir(base_path($this->options['paths']['controller']), 0777, true);
        $this->createFile('controller/controller.php', base_path($this->options['paths']['controller']) . '/' . $this->replace['model']['bread_controller_class'] . '.php');

        // create model file
        if (!file_exists(base_path($this->options['paths']['model']))) mkdir(base_path($this->options['paths']['model']), 0777, true);
        $this->createFile('model.php', base_path($this->options['paths']['model']) . '/' . $this->replace['model']['bread_model_class'] . '.php');

        // create migration file
        $this->createFile('database/migration.php', database_path('migrations/' . date('Y_m_d_000000', time()) . '_create_' . $this->replace['model']['bread_model_variable'] . '_table.php'));

        // create view files
        $this->createViews();

        // add menu item to layout navbar
        $this->updateNavbar();

        // append routes to web
        $this->updateRoutes();
    }

    public function createFile($file, $target)
    {
        $file = base_path($this->options['paths']['stubs']) . '/' . $file;

        if (file_exists($file)) {
            file_put_contents($target, $this->replaceContent($file));
            $this->line('Created file: ' . $target);
        }
    }

    public function createViews()
    {
        $views_folder = base_path($this->options['paths']['stubs']) . '/views';

        if (file_exists($views_folder)) {
            $views = new DirectoryIterator($views_folder);
            $target_folder = base_path($this->options['paths']['views']) . '/' . $this->replace['model']['bread_model_variables'];

            // create target folder if it doesn't exist
            if (!file_exists($target_folder)) {
                mkdir($target_folder, 0777, true);
            }

            // loop through all view stubs and create
            foreach ($views as $view) {
                if (!$view->isDot() && !$view->isDir() && $view->getFilename() != 'navbar.blade.php') {
                    $this->createFile('views/' . $view->getFilename(), $target_folder . '/' . $view->getFilename());
                }
            }
        }
    }

    public function updateNavbar()
    {
        $file = base_path($this->options['paths']['stubs']) . '/views/navbar.blade.php';
        $target = base_path($this->options['paths']['navbar']);
        $hook = '<!-- bread_navbar -->';

        if (file_exists($file) && file_exists($target)) {
            $file_content = $this->replaceContent($file);
            $target_content = file_get_contents($target);

            if (strpos($target_content, $file_content) === false) {
                file_put_contents($target, str_replace($hook, $hook . PHP_EOL . $file_content, $target_content));
                $this->line('Updated file: ' . $target);
            }
        }
    }

    public function updateRoutes()
    {
        $file = base_path($this->options['paths']['stubs']) . '/routes.php';
        $target = base_path($this->options['paths']['routes']);

        if (file_exists($file) && file_exists($target)) {
            $file_content = $this->replaceContent($file);
            $target_content = file_get_contents($target);

            if (strpos($target_content, $file_content) === false) {
                file_put_contents($target, PHP_EOL . PHP_EOL . $file_content, FILE_APPEND);
                $this->line('Updated file: ' . $target);
            }
        }
    }

    public function replaceContent($file)
    {
        $content = file_get_contents($file);
        $content = strtr($content, $this->replace['attributes']);
        $content = strtr($content, $this->replace['model']);

        return $content;
    }
}