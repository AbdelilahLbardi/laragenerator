<?php

namespace AbdelilahLbardi\LaraGenerator\Console\Renders;

use Illuminate\Support\Facades\File;

trait CanGenerateRequest{

	public function requestBlock()
	{
		if ($this->option('without-request'))
			return $this->info('Form request: [ignored]');

        $this->makeRequestFolder();

        $content = $this->getRequestContent();

        $this->createRequestFile($content);
	}

	/*
	* Make namespace Folder
	*/
	public function makeRequestFolder()
	{
		$path = app_path('Http/Requests/' . $this->namespace);
        
        if (!File::isDirectory($path))
            File::makeDirectory($path, 0775, true);
	}

	/*
	* Get the template and replace the expressions
	*/
	public function getRequestContent()
	{
        $template = File::get($this->path('Templates/Request/Request.txt'));

        $field = File::get($this->path('Templates/Request/field.txt'));

        $fields = "";

        $namespace = ($this->namespace != "" ? '\\' . str_replace('/', '\\', $this->namespace) : "");

        foreach($this->fillableArray($this->fillable) as $input)
        	$fields .= str_replace('{{field}}', trim($input), $field);


        return str_replace(
            ['{{namespace}}', '{{fields}}', '{{request}}'], 
            [rtrim($namespace), $fields, str_plural($this->model) . "Request"], $template
        );
	}

	/*
	* Create the rendered template from setContent
	*/
	public function createRequestFile($content)
	{
		$path = app_path('Http/Requests/' . $this->namespace . str_plural($this->model) . 'Request.php');

        if(File::exists($path))
            return $this->info('Request already exists !');

        File::put($path, $content);

		$this->info('Request: [created]');
	}


}