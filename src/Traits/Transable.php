<?php


namespace Fredckl\Transable\src\Traits;


use Fredckl\Transable\Models\I18n;
use Fredckl\Transable\src\Locales;
use Fredckl\Transable\src\Models\Entity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

trait Transable
{

    use TransableRelations, TransableScopes;

    protected $transableAttributes = [];

    protected static $autoload = false;

    public function __construct (array $attributes = [])
    {
        $locales = Locales::getLocales();
        foreach  ($locales as $locale) {
            $this->fillable[] = $locale;
        }

        parent::__construct($attributes);
    }

    public static function bootTransable ()
    {
        static::saving(function (Model $model) {
            $model->savingTranslations();
        });

        static::saved(function (Model $model) {
            return $model->saveTranslations();
        });

        static::retrieved(function (Model $model) {
            return $model->retrieveTranslations();
        });

        static::deleted(function (Model $model) {
            return $model->deleteTranslations();
        });
    }

    abstract function transable (): array;

    protected function savingTranslations ()
    {
        $locales = Locales::getLocales();
        $attributes = $this->getAttributes();
        if (!empty($attributes)) {
            foreach ($locales as $locale) {
                if (isset($attributes[$locale])) {
                    $this->transableAttributes[$locale] = $attributes[$locale];
                    unset($this[$locale]);
                }
            }
        }
    }

    protected function saveTranslations ()
    {
        $fields = $this->transable();

        $transableData = [];

        foreach ($this->transableAttributes as $locale => $attr) {
            foreach ($fields as $field) {
                $model = I18n::where(['locale' => $locale, 'transable_id' => $this->id, 'field' => $field])->first();

                if (!$model) {
                    $model = new i18n;
                }

                if (isset($attr[$field])) {
                    $model->field = $field;
                    $model->content = $attr[$field];
                    $model->locale = $locale;
                    $transableData[] = $model;
                }
            }
        }

        if (!empty($transableData)) {
            $this->translations()->saveMany($transableData);
        }
    }

    public function retrieveTranslations()
    {
        if (static::$autoload) {
            $this->getTranslation(static::$autoload);
        }

        return $this;
    }

    protected function getTranslation(?string $locale = null)
    {
        $locale = $locale ?? static::$autoload;
        $fields = $this->transable();
        $translations = $this->translations()->where('locale', $locale)->get();
        if ($translations->isEmpty()) {
            $model = new Entity();
        } else {
            $data = [];
            foreach ($translations as $translation) {
                if (in_array($translation->field, $fields)) {
                    $data[$translation->field] = $translation->content;
                }
            }
            $model = new Entity($data);
        }

        $this->{$locale} = $model;
    }

    public static function autoTranslate ($locale = null)
    {
        if ($locale !== false) {
            $locales = Locales::getLocales();
            $locale = $locale ?? App::getLocale();
            if (!in_array($locale, $locales)) {
                $locale = false;
            }
        }

        static::$autoload = $locale;
    }

    public function getAllTranslations ()
    {
        $locales = Locales::getLocales();
        foreach ($locales as $locale) {
            $this->getTranslation($locale);
        }

        return $this;
    }

    public function __get ($name)
    {
        $locales = Locales::getLocales();

        if (in_array($name, $locales)) {

            if (!isset($this[$name])) {
                $this->getTranslation($name);
            }

            return $this[$name];

        } elseif (isset($this[$name])) {
            if (static::$autoload) {
                if (!isset($this->{static::$autoload}->$name)) {
                    $this->getTranslation();
                }

                if (!empty($this->{static::$autoload}->$name)) {
                    return $this->{static::$autoload}->$name;
                }
            }
            return  $this[$name];
        }

        return $this;
    }

    public function deleteTranslations ()
    {
        $translations = array_column($this->translations()->get('id')->toArray(), 'id');
        I18n::destroy($translations);
    }

    public static function deleteTranslationsWhenEmptyModel()
    {
        I18n::deleteEmpty();
    }
}
