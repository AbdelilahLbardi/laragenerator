<?php

namespace AbdelilahLbardi\LaraGenerator\Console\Renders;

use Illuminate\Support\Facades\File;

trait CanGenerateController{

	public function controllerBlock()
	{
		if ($this->option('without-controller'))
			return $this->info('Controller: [ignored]');

        $this->makeControllerFolder();

        $content = $this->getControllerContent();

        $this->createControllerFile($content);
	}

	/*
	* Make namespace Folder
	*/
	public function makeControllerFolder()
	{
		$path = app_path('Http/Controllers/' . $this->namespace);
        
        if (!File::isDirectory($path))
            File::makeDirectory($path, 0775, true);
	}

	/*
	* Get the template and replace the expressions
	*/
	public function getControllerContent()
	{
		$usesNamespace = ($this->namespace != "" ? 'use App\Http\Controllers\Controller;' : "");

        $namespace = ($this->namespace != "" ? '\\' . str_replace('/', '\\', $this->namespace) : "");

        $template = File::get($this->path('Templates/Controller/Controller.txt'));

        $template = str_replace('{{//}}', ($this->option('with-flash') ? "" : "//"), $template);

        return str_replace(
            ['{{model}}', '{{variables}}', '{{variable}}', '{{fillable}}', '{{controller}}', '{{namespace}}', '{{usesNamespace}}', '{{viewNamespace}}'], 
            [$this->model, $this->variables, $this->variable, $this->fillable, $this->controller, $namespace, $usesNamespace, $this->viewNamespace], $template
        );
	}

	/*
	* Create the rendered template from setContent
	*/
	public function createControllerFile($content)
	{
		$path = app_path('Http/Controllers/' . $this->namespace . $this->controller . '.php');

        if(File::exists($path))
            return $this->info('Controller already exists !');

        File::put($path, $content);

		$this->info('Controller: [created]');
	}


}