<?php


namespace Fredckl\Transable\src\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait TransableRelations
{

    public function translations (): MorphMany
    {
        return $this->morphMany('Fredckl\Transable\Models\I18n', 'transable');
    }
}
