<?php

namespace AbdelilahLbardi\LaraGenerator\Console\Renders;

use Illuminate\Support\Facades\File;

trait CanGenerateModel{

	public function modelBlock()
	{
		if ($this->option('without-model'))
        	return $this->info('Model: [ignored]');

        $this->makeModelFolder();

        $content = $this->getModelContent();

        $this->createModelFile($content);
	}

	/*
	* Make namespace Folder
	*/
	public function makeModelFolder()
	{
		$path = app_path('Models');
        
        if (!File::isDirectory($path))
            File::makeDirectory($path);
	}

	/*
	* Get the template and replace the expressions
	*/
	public function getModelContent()
	{
		$template = File::get($this->path('Templates/Model/Model.txt'));

        return str_replace(
            ['{{model}}', '{{fillable}}'], 
            [$this->model, $this->fillable], $template
        );
	}

	/*
	* Create the rendered template from setContent
	*/
	public function createModelFile($content)
	{
		$path = app_path('Models/' . $this->model . '.php');

        if(File::exists($path))
            return $this->info('Model already exists !');

        File::put($path, $content);

		$this->info('Model: [created]');
	}


}