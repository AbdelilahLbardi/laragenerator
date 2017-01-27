<?php

namespace AbdelilahLbardi\LaraGenerator\Console\Commands;

use AbdelilahLbardi\LaraGenerator\Traits\Helpers;
use AbdelilahLbardi\LaraGenerator\Console\Renders\CanGenerateController; 
use AbdelilahLbardi\LaraGenerator\Console\Renders\CanGenerateModel; 
use AbdelilahLbardi\LaraGenerator\Console\Renders\CanGenerateRoute; 
use AbdelilahLbardi\LaraGenerator\Console\Renders\CanGenerateView;
use AbdelilahLbardi\LaraGenerator\Console\Renders\CanGenerateMigration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;


class Generator extends Command
{

    use Helpers;
    use CanGenerateController; 
    use CanGenerateModel; 
    use CanGenerateRoute; 
    use CanGenerateView;
    use CanGenerateMigration;

    private $model;
    private $controller; 
    private $variable; 
    private $variables; 
    private $schema; 
    private $fillable; 
    private $namespace;
    private $viewNamespace;
    private $migrationSchema;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:resource
        {model}
        {--S|schema=empty}
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

    /*
    * construct the properties for traits usage.
    */
    public function defineProperties()
    {
        $directories = explode('/', trim($this->argument('model')));
    
        $this->model = last($directories);
        
        array_pop($directories);

        $this->namespace = (trim(implode('/', $directories)) == "" ? "" : trim(implode('/', $directories)) . "/");

        $this->viewNamespace = ($this->namespace == "" ? "" : rtrim(strtolower($this->namespace), '/') . '.');

        $this->schema = trim($this->option('schema'));
        
        $this->variable = trim(strtolower($this->model));
        
        $this->variables = trim(str_plural($this->variable));
        
        $this->controller = trim(ucfirst($this->variables)) . 'Controller';
        
        $this->fillable = $this->fillable($this->schema);

        $this->migrationSchema = [ 'name' => 'create_' . $this->variables . '_table', '--model' => false ];

        if($this->schema != "empty")
            $this->migrationSchema = array_add($this->migrationSchema, '--schema', $this->schema);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $this->defineProperties();

        if(!$this->option('rollback'))
            return $this->build();
        
        $this->destroy();
    }


    /**
     * Build a resource.
     */
    public function build()
    {
        $this->controllerBlock();

        $this->modelBlock();

        $this->viewBlock();

        $this->routeBlock();

        $this->migrationBlock();
    }

    /**
     * Destroy a resource
     */
    public function destroy()
    {
        File::delete(base_path('routes/web/' . strtolower($this->namespace . $this->variables) . '.php'));

        $this->info('Routes: [deleted]');

        File::delete(app_path('Models/' . $this->model . '.php'));

        $this->info('Model: [deleted]');

        File::delete(app_path('Http/Controllers/'. $this->namespace. $this->controller . '.php'));

        $this->info('Controller: [deleted]');

        File::deleteDirectory(resource_path('views/' . $this->namespace. $this->variables));

        $this->info('Views: [deleted]');
    }

}