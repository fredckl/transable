<?php


namespace Fredckl\Transable\src;


class Locales
{
    public static function getLocales ()
    {
        $locales = config('transable.locales');
        $defaultLocale = config('transable.default_locale');
        if (empty($locales) || !is_array($locales)) {
            return [];
        }
        $locales = \array_diff($locales, [$defaultLocale]);

        return $locales;
    }
}
