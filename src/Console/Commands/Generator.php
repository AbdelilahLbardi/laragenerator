<?php

namespace AbdelilahLbardi\LaraGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;


class Generator extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:resource
        {model}
        {--S|schema=""}
        {--R|rollback}
        {--with-flash}
        {--without-model}
        {--without-controller}
        {--without-views}
        {--without-migration}
        {--without-routes}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bootstrap your app with ready Models, Controllers and Views';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $directories = explode('/', $this->argument('model'));
        
        $model = last($directories);
        
        array_pop($directories);

        $namespace = implode('/', $directories);

        $schema = $this->option('schema');
        
        $variable = strtolower($model);
        
        $variables = str_plural($variable);
        
        $controller = ucfirst($variables) . 'Controller';
        
        $fillable = $this->schemaToFillable($schema);

        if(!$this->option('rollback'))
            return $this->build($model, $controller, $variable, $variables, $schema, $fillable, $namespace);
        
        $this->destroy($controller, $model, $variables, $namespace);
    }


    /**
     * Initiate the building of the Namespace, Controller, Model, Views, Routes and Migration.
     *
     * @param $model
     * @param $controller
     * @param $variable
     * @param $variables
     * @param $schema
     * @param $fillable
     * @param $namespace
     */
    public function build($model, $controller, $variable, $variables, $schema, $fillable, $namespace)
    {
        $this->ControllerBlock($model, $controller, $variable, $variables, $fillable, $namespace);

        $this->ModelBlock($model, $fillable);

        $this->ViewsBlock($variables, $namespace);

        $this->RoutesBlock($namespace, $variables, $controller);

        $this->MigrationBlock($variables, $schema);
    }

    /**
     * Destroy the created resources
     *
     * @param $controller
     * @param $model
     * @param $variables
     * @param $namespace
     */
    public function destroy($controller, $model, $variables, $namespace)
    {
        $namespace = ($namespace != "" ? $namespace . '/' : "");

        File::delete(base_path('routes/' . $namespace . $variables . '.php'));

        $this->info('Routes Deleted!');

        File::delete(app_path('Models/' . $model . '.php'));

        $this->info('Model Deleted!');

        File::delete(app_path('Http/Controllers/'. $namespace. $controller . '.php'));

        $this->info('Controller Deleted!');

        File::deleteDirectory(resource_path('views/' . $namespace. $variables));

        $this->info('Views folder deleted!');
    }

    /*
     * Generate Controller block
     * */
    public function ControllerBlock($model, $controller, $variable, $variables, $fillable, $namespace)
    {
        if (!$this->option('without-controller'))
        {
            if(!empty($namespace))
                $this->makeControllersNamespace($namespace);

            $controllerContent = $this->getController($model, $controller, $variable, $variables, $fillable, $namespace);

            $this->makeController($namespace, $controller, $controllerContent);

            return $this->info('Controller: [created]');
        }
        $this->info('Controller: [ignored]');
    }

    /*
     * Generate Model block
     * */
    public function ModelBlock($model, $fillable)
    {
        if (!$this->option('without-model'))
        {
            $this->makeModelsNamespace();

            $modelContent = $this->getModel($model, $fillable);

            $this->makeModel($model, $modelContent);

            return $this->info('Model: [created]');
        }

        $this->info('Model: [ignored]');
    }

    /*
     * Generate Views Block
     * */
    public function ViewsBlock($variables, $namespace)
    {
        if(!$this->option('without-views'))
        {
            $viewsContent = $this->getViews();

            $this->makeViews($variables, $viewsContent, $namespace);

            return $this->info('Views: [create]');
        }
        $this->info('Views: ignored]');
    }

    /*
     * Generate Routes Block
     * */
    public function RoutesBlock($namespace, $variables, $controller)
    {
        if (!$this->option('without-routes'))
        {
            $this->makeRoutesFolder($namespace);

            $routesContent = $this->getRoutes($variables, $controller, $namespace);

            $this->makeRoutes($variables, $routesContent, $namespace);

            return $this->info('Routes: [created]');
        }
        $this->info('Routes: [ignored]');
    }

    /*
     * Generate the migration
     * */
    public function MigrationBlock($variables, $schema)
    {
        if (!$this->option('without-migration'))
        {
            $this->callSilent('make:migration:schema', [
                'name' => 'create_' . $variables . '_table', '--schema' => $schema, '--model' => false
            ]);

            return $this->info('Migration: [created]');
        }else{
            $this->info('Migration: [ignored]');
        }
    }

    /**
     * @param $model
     * @param $controller
     * @param $variable
     * @param $variables
     * @param $fillable
     * @param $namespace
     * @return mixed
     */
    public function getController($model, $controller, $variable, $variables, $fillable, $namespace)
    {
        $usesNamespace = ($namespace != "" ? 'use App\Http\Controllers\Controller;' : "");

        $namespace = ($namespace != "" ? '\\' . str_replace('/', '\\', $namespace) : "");

        $template = File::get($this->path('Templates/Controller/Controller.txt'));

        $template = str_replace('{{//}}', ($this->option('with-flash') ? "" : "//"), $template);

        return str_replace(
            ['{{model}}', '{{variables}}', '{{variable}}', '{{fillable}}', '{{controller}}', '{{namespace}}', '{{usesNamespace}}'], 
            [$model, $variables, $variable, $fillable, $controller, $namespace, $usesNamespace], $template
        );
    }

    /**
     * @param $namespace
     * @param $controller
     * @param $controllerContent
     */
    public function makeController($namespace, $controller, $controllerContent)
    {
        $namespace = ($namespace != "" ? $namespace . '/' : "");

        $path = app_path('Http/Controllers/' . $namespace . $controller . '.php');

        if(File::exists($path))
            return $this->error($path . ' Already exists !');

        File::put($path, $controllerContent);
    }


    /**
     * @param $model
     * @param $fillable
     * @return mixed
     */
    public function getModel($model, $fillable)
    {
        $template = File::get($this->path('Templates/Model/Model.txt'));

        return str_replace(
            ['{{model}}', '{{fillable}}'], 
            [$model, $fillable], $template
        );
    }

    /**
     * @param $model
     * @param $modelContent
     */
    public function makeModel($model, $modelContent)
    {
        $path = app_path('Models/' . $model . '.php');

        if(File::exists($path))
            return $this->error($path . ' Already exists !');

        File::put($path, $modelContent);
    }

    /**
     * @return array
     */
    public function getViews()
    {
        return [
                'index.blade.php' => File::get($this->path('Templates/View/index.txt')),
                'create.blade.php' => File::get($this->path('Templates/View/create.txt')),
                'edit.blade.php' => File::get($this->path('Templates/View/edit.txt')),
        ];
    }

    /**
     * @param $variables
     * @param $views
     * @param $namespace
     */
    public function makeViews($variables, $views, $namespace)
    {
        $namespace = ($namespace != "" ? strtolower($namespace) . '/' : "");

        File::makeDirectory(resource_path('views/' . $namespace . $variables), 0775, true);

        foreach ($views as $key => $value) {

            $path = resource_path('views/' . $namespace . $variables . '/' . $key);

            if(File::exists($path))
            {
                $this->error(resource_path('views/' . $namespace . $variables . '/' . $key));

                continue;
            }

            File::put( $path, $value);
        }

    }


    /**
     * @param $variables
     * @param $controller
     * @param $namespace
     * @return mixed
     */
    public function getRoutes($variables, $controller, $namespace)
    {
        $template = File::get($this->path('Templates/routes.txt'));

        $namespace = ($namespace != "" ? "'namespace' => '" . str_replace('/', '\\', $namespace) . "'" : "");

        return str_replace(
            ['{{variables}}', '{{controller}}', '{{namespace}}'], 
            [$variables, $controller, $namespace], $template
        );
    }

    /**
     * @param $namespace
     */
    public function makeRoutesFolder($namespace)
    {
        $path = base_path('routes/web/' . strtolower($namespace));
        
        if (!File::isDirectory($path))
            File::makeDirectory($path, 0775, true);
    }

    /**
     * @param $variables
     * @param $routesContent
     * @param $namespace
     */
    public function makeRoutes($variables, $routesContent, $namespace)
    {
        $namespace = ($namespace != "" ? strtolower($namespace) . '/' : "");

        File::put( base_path('routes/web/' . $namespace . $variables . '.php'), $routesContent);
    }


    /**
     * @param $namespace
     */
    public function makeControllersNamespace($namespace)
    {
        $path = app_path('Http/Controllers/' . $namespace);
        
        if (!File::isDirectory($path))
            {
                File::makeDirectory($path, 0775, true);
                Log::debug($path . ' is created');
            }
    }

    /**
     * Make the Models namespace
     */
    public function makeModelsNamespace()
    {
        $path = app_path('Models');
        
        if (!File::isDirectory($path))
            File::makeDirectory($path);
    }

    /**
     * Convert the schema argument to the fillable array
     *
     * @param $schema
     * @return string
     */
    public function schemaToFillable($schema)
    {
        $array = explode(',', $schema);
        
        $fillable = "";

        foreach ($array as $value) 
        {
            $variables = explode(':', $value);

            $fillable .= "'" . str_replace(" ", "", $variables[0]) . "', ";
        }

        return $fillable;
    }

    /**
     * search if the Templates folder has been published or not
     *
     * @param $path
     * @return string
     */
    public function path($path)
    {
        if(File::exists(base_path($path)))
            return base_path($path);

        return __DIR__ . '/../../' . $path;
    }

}