<?php

namespace Sharenjoy\Cmsharenjoy\Service\Translatable;

use Illuminate\Support\Str;
use Sharenjoy\Cmsharenjoy\Service\Translatable\Events\TranslationHasBeenSet;
use Sharenjoy\Cmsharenjoy\Service\Translatable\Exceptions\AttributeIsNotTranslatable;

trait HasTranslations
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        if (!$this->isTranslatableAttribute($key)) {
            return parent::getAttributeValue($key);
        }

        return $this->getTranslation($key, current_language());
    }

    /**
     * @param string $key
     * @param string $locale
     *
     * @return mixed
     */
    public function translate(string $key, string $locale = '')
    {
        return $this->getTranslation($key, $locale);
    }

    /**
     * @param string $key
     * @param string $locale
     *
     * @return mixed
     */
    public function getTranslation(string $key, string $locale)
    {
        $locale = $this->normalizeLocale($key, $locale);

        $translations = $this->getTranslations($key);

        $translation = $translations[$locale] ?? '';

        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $translation);
        }

        return $translation;
    }

    public function getTranslations($key) : array
    {
        $this->guardAgainstUntranslatableAttribute($key);

        return json_decode($this->getAttributes()[$key] ?? '' ?: '{}', true);
    }

    /**
     * Determine that is translation there
     *
     * @param  string  $value
     * @param  string  $locale
     *
     * @return boolean
     */
    protected function hasTranslation(string $value, string $locale) : bool
    {
        return $this->getTranslation($value, $locale) ? true : false;
    }

    /**
     * @param string $key
     * @param string $locale
     * @param $value
     *
     * @return $this
     */
    public function setTranslation(string $key, string $locale, $value)
    {
        $this->guardAgainstUntranslatableAttribute($key);

        $translations = $this->getTranslations($key);

        $oldValue = $translations[$locale] ?? '';

        if ($this->hasTranslationSetMutator($key)) {
            $method = 'set'.Str::studly($key).'TranslationAttribute';
            $value = $this->{$method}($value);
        }

        $translations[$locale] = $value;

        // $this->attributes[$key] = $this->asJson($translations);

        // event(new TranslationHasBeenSet($this, $key, $locale, $oldValue, $value));

        return $translations;
    }

    protected function hasTranslationSetMutator($key)
    {
        return method_exists($this, 'set'.Str::studly($key).'TranslationAttribute');
    }

    /**
     * @param string $key
     * @param array  $translations
     *
     * @return $this
     */
    public function setTranslations(string $key, array $translations)
    {
        $this->guardAgainstUntranslatableAttribute($key);

        foreach ($translations as $locale => $translation) {
            $this->setTranslation($key, $locale, $translation);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param string $locale
     *
     * @return $this
     */
    public function forgetTranslation(string $key, string $locale)
    {
        $translations = $this->getTranslations($key);

        unset($translations[$locale]);

        $this->setAttribute($key, $translations);

        return $this;
    }

    public function getTranslatedLocales(string $key) : array
    {
        return array_keys($this->getTranslations($key));
    }

    public function isTranslatableAttribute(string $key) : bool
    {
        return in_array($key, $this->getTranslatableAttributes());
    }

    protected function guardAgainstUntranslatableAttribute(string $key)
    {
        if (!$this->isTranslatableAttribute($key)) {
            throw AttributeIsNotTranslatable::make($key, $this);
        }
    }

    protected function normalizeLocale(string $key, string $locale) : string
    {
        if (in_array($locale, $this->getTranslatedLocales($key))) {
            return $locale;
        }

        if (!is_null($fallbackLocale = config('laravel-translatable.fallback_locale'))) {
            return $fallbackLocale;
        }

        return $locale;
    }

    public function getTranslatableAttributes() : array
    {
        return is_array($this->translatable)
            ? $this->translatable
            : [];
    }

    public function getCasts() : array
    {
        return array_merge(
            parent::getCasts(),
            array_fill_keys($this->getTranslatableAttributes(), 'array')
        );
    }
}