<?php

namespace AbdelilahLbardi\LaraGenerator\Console\Renders;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

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

		$relations = "";

		foreach ($this->relationship as $value) 
			{
				$key = trim(key($value));

				$variables = $value[key($value)];

				$relations.= str_replace(
					['{{variable}}', '{{variables}}', '{{model}}'], 
					[str_singular($variables), str_plural($variables), ucfirst(str_singular($variables))], 
					File::get($this->path('Templates/Relationship/'. $key .'.txt')));
			}

        return str_replace(
            ['{{model}}', '{{fillable}}', '{{relationships}}'], 
            [$this->model, $this->fillable, $relations], $template
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