<?php

namespace AbdelilahLbardi\LaraGenerator\Console\Renders;

use Illuminate\Support\Facades\File;

trait CanGenerateRoute{

	public function routeBlock()
	{
        if ($this->option('without-routes'))
        	return $this->info('Routes: [ignored]');

        $this->makeRouteFolder();

        $content = $this->getRouteContent();

        $this->createRouteFile($content);
	}

	/*
	* Make namespace Folder
	*/
	public function makeRouteFolder()
	{
		$path = base_path('routes/web/' . strtolower($this->namespace));

        if (!File::isDirectory($path))
            File::makeDirectory($path, 0775, true);
	}

	/*
	* Get the template and replace the expressions
	*/
	public function getRouteContent()
	{
		$template = File::get($this->path('Templates/routes.txt'));

        $namespace = ($this->namespace != "" ? "'namespace' => '" . str_replace('/', '\\', $this->namespace) . "'" : "");

        return str_replace(
            ['{{variables}}', '{{controller}}', '{{namespace}}'], 
            [$this->variables, $this->controller, $namespace], $template
        );
	}

	/*
	* Create the rendered template from setContent
	*/
	public function createRouteFile($content)
	{
		$path = base_path('routes/web/' . strtolower($this->namespace . $this->variables) . '.php');

        if(File::exists($path))
            return $this->info('route already exists !');

        File::put($path, $content);

		$this->info('Route: [created]');
	}


}