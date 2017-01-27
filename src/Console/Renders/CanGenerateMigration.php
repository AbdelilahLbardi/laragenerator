<?php

namespace AbdelilahLbardi\LaraGenerator\Console\Renders;

use Illuminate\Support\Facades\File;

trait CanGenerateMigration{

	public function migrationBlock()
	{
		if ($this->option('without-migration'))
            return $this->info('Migration: [ignored]');

        $this->callSilent('make:migration:schema', $this->migrationSchema);

        $this->info('Migration: [created]');
	}

}