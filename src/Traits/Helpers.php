<?php

namespace AbdelilahLbardi\LaraGenerator\Traits;

use Illuminate\Support\Facades\File;

trait Helpers
{

    /*
    * Transform schema option to a fillable string
    */
	public function fillable()
	{
        if($this->schema == "empty")
            return "";

		$array = explode(',', $this->schema);
        
        $fillable = "";

        foreach ($array as $value) 
        {
            $variables = explode(':', $value);

            $fillable .= "'" . str_replace(" ", "", trim($variables[0])) . "', ";
        }

        return $fillable;
	}


    /*
    * Transform schema to a fillable array
    */
    public function fillableArray()
    {
        $array = explode(',', $this->schema);
        
        $fillable = [];

        foreach ($array as $value) 
        {
            $variables = explode(':', $value);

            $fillable[] = trim($variables[0]) ;
        }
        return $fillable;
    }

    /*
    * Used to look for if the template file published or
    * to use the default template files instead
    */
	public function path($path)
    {
        if(File::exists(base_path($path)))
            return base_path($path);

        return __DIR__ . '/../' . $path;
    }

    /*
    * Create the HTML Table header
    */
    public function tableHeader()
    {
        $fields = $this->fillableArray($this->schema);
        
        $cell = File::get($this->path('Templates/View/headerCell.txt')) . "\n";

        $rows = str_replace('{{field}}', 'id', $cell);

        foreach($fields as $field)
            $rows .= str_replace('{{field}}', trim($field), $cell);

        $rows .= str_replace('{{field}}', 'actions', $cell);

        return $rows;
    }

    /*
    * Create the HTML Table content
    */
    public function tableContent()
    {
        $fields = $this->fillableArray($this->schema);

        $cell = File::get($this->path('Templates/View/contentCell.txt')) . "\n";

        $rows = str_replace(
            ['{{variables}}', '{{field}}'], 
            [$this->variables, 'id'], $cell
        );

        foreach($fields as $field)
            $rows .= str_replace(
                ['{{variables}}', '{{field}}'], 
                [$this->variables, trim($field)], $cell
            );

        return $rows;
    }

    /*
    * return inputs string
    */
    public function createInputs()
    {
        $fields = $this->fillableArray($this->schema);

        $input = File::get($this->path('Templates/View/createInput.txt')) . "\n";

        $block = "";

        foreach($fields as $field)
            $block .= str_replace(
                ['{{variable}}', '{{field}}'], 
                [$this->variable, $field], $input);
    
        return $block;
    }

    /*
    *   return inputs string with value
    */
    public function editInputs()
    {
        $fields = $this->fillableArray($this->schema);

        $input = File::get($this->path('Templates/View/editInput.txt')) . "\n";

        $block = "";

        foreach($fields as $field)
            $block .= str_replace(
                ['{{variable}}', '{{field}}'], 
                [$this->variable, $field], $input);
    
        return $block;
    }

}