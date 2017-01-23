# Laragenerator

## Laravel 5.3 generator

Building a laravel project comes from an idea. So why not to build the main idea behavior and leave the rest to Laragenerator in order to bootstrap your project. 
This package helps you to focus more about your project idea. <b>DRY</b>.

The current features are :
````
- Create Namespaces.
- Create Controllers.
- Create Models.
- Create Migrations.
- Routes Files.
- Views files and folder.
````

If you have any suggestions please let me know : https://github.com/AbdelilahLbardi/Laragenerator/pulls.

## Installation

First, pull in the package through Composer.

```
"require": {
    "abdelilahlbardi/laragenerator": "1.*"
}
```

And then run :

```
composer update
```

After that, include the service provider within `config/app.php`.

```
'providers' => [
    Laracasts\Generators\GeneratorsServiceProvider::class,
    AbdelilahLbardi\LaraGenerator\Providers\LaraGeneratorServiceProvider::class,
];
```

Note that Laragenerator use (schema generator)[https://github.com/laracasts/Laravel-5-Generators-Extended] of laracasts package to generate database migrations.

## Usage

The package extends Laravel Artisan and add `generate:app` command :

Here's a quick example of how to bootstrap your laravel idea:

```bash
php artisan generate:app \
--model=Article --controller=ArticlesController \
--schema="title:string, content:text, slug:string:unique, user_id:integer:foreign" \
--namespace=Backend
```

<table>
	<tr>
		<td>Name</td>
		<td>Exemple</td>
		<td>Usage</td>
	</tr>
	<tr>
		<td>Model</td>
		<td>--model=Article</td>
		<td><b>Required</b></td>
	</tr>
	<tr>
		<td>Controller</td>
		<td>--controller=ArticlesController</td>
		<td><b>Required</b></td>
	</tr>
	<tr>
		<td>Schema</td>
		<td>--schema="title:string, content:text, user_id:integer:foreign"</td>
		<td><b>Required</b></td>
	</tr>
	<tr>
		<td>Namespace</td>
		<td>--namespace=Backend</td>
		<td><b>Required</b></td>
	</tr>
	<tr>
		<td>Use flash</td>
		<td>--use-flash</td>
		<td>optional</td>
	</tr>
	<tr>
		<td>Rollback</td>
		<td>--rollback</td>
		<td>optional</td>
	</tr>
</table>

## Examples

- [Bootstrapping without flash](#bootstrapping-without-flash)
- [Bootstrapping with flash](#bootstrapping-with-flash)
- [Delete the created files](#rollback)


### Bootstrapping Without flash
  ```bash
php artisan generate:app --model=Article --controller=ArticlesController \
                        --schema="title:string, content:text, slug:string:unique, user_id:integer:foreign" \
                        --namespace=Backend
```
You will notice additional files and folders appear in your project :

 - `Controllers/Backend/AriclesController.php` : Here your generated controller inside the namespace that your specified.
 - `app/Models/Aritlce` : New Models folder the your Models.
 - `resources/views/articles/index.blade.php` : index view which is empty.
 - `resources/views/articles/create.blade.php` : empty create view.
 - `resources/views/articles/edit.blade.php` : empty edit view.
 - `routes/web/articles.php` : Ready routes generated.
 - `database/migrations/yyyy-mm-dd-create_articles_table.php` : Here your migration.


### Bootstrapping With flash
  ```bash
php artisan generate:app --model=Article --controller=ArticlesController \
                        --schema="title:string, content:text, slug:string:unique, user_id:integer:foreign" \
                        --namespace=Backend --use-flash
```
This will generate the same files but won't comment the flash code line.

### Rollback

  ```bash
php artisan generate:app --model=Article --controller=ArticlesController \
                        --schema="title:string, content:text, slug:string:unique, user_id:integer:foreign" \
                        --namespace=Backend --rollback
```
With the Laragenerator `--rollback` option you don't need to delete the generated folders manually. It does all the job for you.
Notice that if you have more controllers this options doesn't delete all the controllers in the namespace.

## Templates publishing

You may want to customize the templates like the following artisan command:

```bash
php artisan endor:publish --tag=templates
```

This will add to your app a new folder called `Tempaltes` where you will find the following files:

 - `Templates/Controller/Controller.txt` : Controllers Template.
 - `Templates/Model/Model.txt` : Models Template.
 - `Templates/View/index.txt` : Index View Template.
 - `Templates/View/create.txt` : Create View Template.
 - `Templates/View/edit.txt` : Edit View Template.
 - `Templates/routes.txt`: Routes File Tempalte.

## Schema

Since Laragenerator uses (Laracasts schema generator)[https://github.com/laracasts/Laravel-5-Generators-Extended#migrations-with-schema] to generate migration and schema you can find the its (documentation)[https://github.com/laracasts/Laravel-5-Generators-Extended#migrations-with-schema] to know more about what to fill in `--schema` option.
