<?php

namespace AbdelilahLbardi\LaraGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;


class Generator extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:app
        {--rollback}
        {--model=: Model}
        {--controller=: Model Controller}
        {--schema=: Migration Schema}
        {--namespace=: Controller Namespace}
        {--use-flash}
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
        $model = $this->option('model');
        $schema = $this->option('schema');
        $controller = $this->option('controller');
        $namespace = $this->option('namespace');
        $fillable = $this->schemaToFillable($schema);
        $variable = strtolower($model);
        $variables = str_plural($variable);

        if(!$this->option('rollback'))
            return $this->build($model, $controller, $variable, $variables, $schema, $fillable, $namespace);
        
        $this->destroy($controller, $model, $variables, $namespace);
    }

    

    public function build($model, $controller, $variable, $variables, $schema, $fillable, $namespace)
    {
        
        $this->makeControllersNamespace($namespace);
        $this->makeModelsNamespace();

        $this->info('Controllers namespace created!');

        $controllerContent = $this->getController($model, $controller, $variable, $variables, $fillable, $namespace);
        $this->makeController($namespace, $controller, $controllerContent);
        
        $this->info($controller . ' created in ' . $namespace . ' namespace !');

        $modelContent = $this->getModel($model, $fillable);
        $this->makeModel($model, $modelContent);

        $this->info($model . ' created in Models namespace !');

        $viewsContent = $this->getViews();
        $this->makeViews($variables, $viewsContent);
        
        $this->info($variables . ' views folder created!');

        $this->makeRoutesFolder();
        $routesContent = $this->getRoutes($variables, $controller, $namespace);
        $this->makeRoutes($variables, $routesContent);

        $this->info($variables . ' routes created!');

        $this->call('make:migration:schema', [
            'name' => 'create_' . $variables . '_table', '--schema' => $schema, '--model' => false
        ]);
    }

    public function destroy($controller, $model, $variables, $namespace)
    {
        File::delete(base_path('routes/' . $variables . '.php'));

        $this->info('Routes Deleted!');

        File::delete(app_path('Models/' . $model . '.php'));

        $this->info('Model Deleted!');

        File::delete(app_path('Http/Controllers/'. $namespace. '/' . $controller . '.php'));

        $this->info('Controller Deleted!');

        File::deleteDirectory(resource_path('views/' . $variables));

        $this->info('Views folder deleted!');
    }

    public function getController($model, $controller, $variable, $variables, $fillable, $namespace)
    {
        $template = File::get($this->path('Templates/Controller/Controller.txt'));

        $template = str_replace('{{//}}', ($this->option('use-flash') ? "" : "//"), $template);

        return str_replace(
            ['{{model}}', '{{variables}}', '{{variable}}', '{{fillable}}', '{{controller}}', '{{namespace}}'], 
            [$model, $variables, $variable, $fillable, $controller, $namespace], $template
        );
    }

    public function makeController($namespace, $controller, $controllerContent)
    {
        File::put(app_path('Http/Controllers/' . $namespace . '/' . $controller . '.php'), $controllerContent);
    }


    public function getModel($model, $fillable)
    {
        $template = File::get($this->path('Templates/Model/Model.txt'));

        return str_replace(
            ['{{model}}', '{{fillable}}'], 
            [$model, $fillable], $template
        );
    }

    public function makeModel($model, $modelContent)
    {
        File::put(app_path('Models/' . $model . '.php'), $modelContent);
    }

    public function getViews()
    {
        return [
                'index.blade.php' => File::get($this->path('Templates/View/index.txt')),
                'create.blade.php' => File::get($this->path('Templates/View/create.txt')),
                'edit.blade.php' => File::get($this->path('Templates/View/edit.txt')),
        ];
    }

    public function makeViews($variables, $views)
    {
        File::makeDirectory(resource_path('views/' . $variables));

        foreach ($views as $key => $value) 
            File::put( resource_path('views/' . $variables . '/' . $key), $value);

    }


    public function getRoutes($variables, $controller, $namespace)
    {
        $template = File::get($this->path('Templates/routes.txt'));

        return str_replace(
            ['{{variables}}', '{{controller}}', '{{namespace}}'], 
            [$variables, $controller, $namespace], $template
        );
    }

    public function makeRoutesFolder()
    {
        $path = base_path('routes/web');
        
        if (!File::isDirectory($path))
            File::makeDirectory($path);
    }

    public function makeRoutes($variables, $routesContent)
    {
        File::put( base_path('routes/web/' . $variables . '.php'), $routesContent);
    }


    public function makeControllersNamespace($namespace)
    {
        $path = app_path('Http/Controllers/' . $namespace);
        
        if (!File::isDirectory($path))
            File::makeDirectory($path);
    }

    public function makeModelsNamespace()
    {
        $path = app_path('Models');
        
        if (!File::isDirectory($path))
            File::makeDirectory($path);
    }

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

    public function path($path)
    {

        if(File::exists(base_path($path)))
            return base_path($path);

        return __DIR__ . '/../../' . $path;
    }

}