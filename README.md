# Transable Package
##  very simple to translate all fields from your existing tables
____
### Installation
via composer
```php
composer require fredckl/transable
```

add ServiceProvider to your config file app.php

```php
"providers" => [
    ...

    /*
     * Package Service Providers...
     */
    Fredckl\Transable\ServiceProvider::class,
    
    ...
]
``` 

execute migration
```php
php artisan migrate
```

import configuration
```php
php artisan vendor:publish --tag=transable
```

define your languages translatable in your transable.php
```php
<?php
return [
    "default_locale" => "en", // Change if you your default locale is different
    "locales" => [
    "fr",
    "es",
    /// ...
]
];
```


### How to use

add transable trait to your class
```php

namespace App;

use Fredckl\Transable\src\Traits\Transable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Transable; // Import Trait

    protected $fillable = ['title', 'content'];
    
    /**
     * Define fields translatable
     */
    public function transable (): array
    {
        return [
            'title', 
            'content'
        ];
    }
}
```

work with translations
```php
$post = App\Post::find(1);
echo $post->title; // return "Hello"
echo $post->content; // return "Content"
echo $post->fr->title; // return "Hello", default value EN because the value not exists

$post->fr->title = "Salut";
$post->fr->content = "contenu";
$post->es->title = "Hola";

$post->save(); true

echo $post->fr->title; // return "Salut"
echo $post->fr->content; // return "contenu"
echo $post->es->title; // return "Hola"  
echo $post->es->content; // return "Contents", default value EN because the value not exists
```

save records
```php
$data = [
    'title' => 'Hello',
    'content' => 'Contents',
    'fr' => [
        'title' => 'Salut',
        'content' => 'contenu'
    ],
    'es' => [
        'title' => 'Hola',
        'content' => 'contenido'
    ]
];
App\Post::create($data);

$post = App\Post::where('title', 'Hello')->first();
echo $post->fr->title; // return "Salut";
```

automatic load translation
```php
App::setLocale('fr');
App\Post::autoTranslate();
// OR without App::setLocale
App\Post::autoTranslate('fr');

$post = App\Post::where('title', 'Hello')->first();
echo $post->title; // return "Salut"
```

finder
```php
App\Post::translated(); // retrieve all post translated
App\Post::doesntHaveTranslations(); // retrieve all post without translations

App\Post::whereTranslation($field, $value); // return matched post
```

deleting
```php
$post->delete(); // delete post and all translations
App\Post::deleteTranslationsWhenEmptyModel(); delete all translations without model
// OR 
Fredckl\Transable\Models\I18n::deleteEmpty(); 
```

This project is being tested. It is not advisable to use it in production.
All suggestions are welcome as well as contributions.

