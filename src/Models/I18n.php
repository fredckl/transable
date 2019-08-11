<?php


namespace Fredckl\Transable\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class I18n extends Model
{
    protected $fillable = ['field', 'content', 'locale'];

    public $timestamps = false;

    public function parent()
    {
        return $this->morphTo('transable');
    }

    public static function deleteEmpty()
    {
        $translations = I18n::all();
        foreach ($translations as $trans) {
            if ($trans->parent()->get()->isEmpty()) {
                $trans->delete();
            }
        }
    }
}
