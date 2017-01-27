<?php

namespace AbdelilahLbardi\LaraGenerator\Console\Renders;

use Illuminate\Support\Facades\File;

trait CanGenerateView{

	public function viewBlock()
	{
		if($this->option('without-views'))
        	return $this->info('Views: ignored]');

        $this->makeViewFolder();
        	
        $content = $this->getViewContent();

        $this->createViewFile($content);
	}

	/*
	* Make namespace Folder
	*/
	public function makeViewFolder()
	{
		$path = resource_path('views/' . strtolower($this->namespace) . $this->variables);

        if (!File::isDirectory($path))
            File::makeDirectory($path, 0775, true);
	}

	/*
	* Get the template and replace the expressions
	*/
	public function getViewContent()
	{
		return [
            'index.blade.php' => $this->getIndexViewContent(),
            'create.blade.php' => $this->getCreateViewContent(),
            'edit.blade.php' => $this->getEditViewContent(),
        ];
	}

	/*
	* Create the rendered template from setContent
	*/
	public function createViewFile($views)
	{
		foreach ($views as $key => $value) {

            $path = resource_path('views/' . strtolower($this->namespace . $this->variables) . '/' . $key);

            if(File::exists($path))
            {
                $this->info('View ' . $key . ' already exists!');

                continue;
            }

            File::put( $path, $value);

			$this->info('View '. $key .': [created]');
        }
	}

	public function getIndexViewContent()
	{
		$template = File::get($this->path('Templates/View/index.txt'));

        return str_replace(
            ['{{header}}', '{{content}}', '{{variables}}', '{{variable}}'], 
            [$this->tableHeader(), $this->tableContent(), $this->variables, $this->variable], $template
        );
	}

	public function getCreateViewContent()
	{
		$template = File::get($this->path('Templates/View/create.txt'));

        return str_replace(
            ['{{variables}}', '{{inputs}}'], 
            [$this->variables, $this->createInputs()], $template
        );
	}

	public function getEditViewContent()
	{
		$template = File::get($this->path('Templates/View/edit.txt'));

        return str_replace(
            ['{{variables}}', '{{variable}}', '{{inputs}}'], 
            [$this->variables, $this->variable, $this->editInputs()], $template
        );
	}


}