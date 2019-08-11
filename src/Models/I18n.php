<?php


namespace Fredckl\Transable\Models;


use Illuminate\Database\Eloquent\Model;

class I18n extends Model
{
    protected $fillable = ['field', 'content', 'locale'];

    public $timestamps = false;
}
