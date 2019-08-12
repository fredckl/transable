<?php


namespace Fredckl\Transable\src\Traits;



use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

trait TransableScopes
{

    public function scopeWhereTranslation (Builder $query, $column, $operator = null, $value = null, ?string $locale = null, $method = "whereHas")
    {
        $operators = $query->getQuery()->operators;
        if (!in_array(\strtolower($operator), $operators)) {
            $method = $locale ?? $method;
            $locale = $value;
            $value = $operator;
            $operator = "=";
        }

        return $query->$method('translations', function (Builder $query) use ($column, $operator, $value, $locale) {

            if (!is_array($column)) {
                $column = [
                    'field' => $column,
                    ['content', $operator, $value]
                ];
            }
            $query->where($column);

            if ($locale) {
                $query->where('locale', '=', $locale);
            }
        });
    }
//    public function scopeWhereTranslation__ (Builder $query, $translationField, $value = null, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
//    {
//        return $query->$method('translations', function (Builder $query) use ($translationField, $value, $locale, $operator) {
//            if (is_array($translationField)) {
//                $conditions = $translationField;
//            } else {
//                $conditions = [
//                    'field' => $translationField,
//                    ['content', $operator, $value]
//                ];
//            }
//            $query->where($conditions);
//
//            if ($locale) {
//                $query->where('locale', '=', $locale);
//            }
//        });
//    }

    public function scopeOrWhereTranslation(Builder $query, string $column, $operator, $value, ?string $locale = null)
    {
        return $this->scopeWhereTranslation($query, $column, $operator, $value, $locale, 'orWhereHas');
    }
    public function scopeOrWhereTranslationLike(Builder $query, string $column,  $value, ?string $locale = null)
    {
        return $this->scopeWhereTranslation($query, $column, 'like', $value, $locale, 'orWhereHas');
    }


    public function scopeTranslatedIn(Builder $query, ?string $locale = null)
    {
        $locale = $locale ?? App::getLocale();
        return $query->whereHas('translations', function (Builder $q) use ($locale) {
            $q->where('locale', '=', $locale);
        });
    }

    public function scopeTranslated(Builder $query)
    {
        return $query->has('translations');
    }

    public function scopeDoesntHaveTranslations (Builder $query)
    {
        return $query->doesntHave('translations');
    }
}
