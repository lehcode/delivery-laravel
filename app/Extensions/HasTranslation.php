<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 14:55
 */

namespace App\Extensions;

use Illuminate\Support\Str;
use Spatie\Translatable\Events\TranslationHasBeenSet;
use Spatie\Translatable\Exceptions\AttributeIsNotTranslatable;

/**
 * Class HasTranslations
 * @package App\Extensions
 */
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

        return $this->getTranslation($key, config('app.locale'));
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

    /***
     * @param string $key
     * @param string $locale
     * @param bool $useFallbackLocale
     *
     * @return mixed
     */
    public function getTranslation(string $key, string $locale, bool $useFallbackLocale = true)
    {
        $locale = $this->normalizeLocale($key, $locale, $useFallbackLocale);

        $translations = $this->getTranslations($key);

        $translation = $translations[$locale] ?? '';

        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $translation);
        }

        return $translation;
    }

    /**
     * @param string $key
     * @param string $locale
     * @return mixed
     */
    public function getTranslationWithFallback(string $key, string $locale)
    {
        return $this->getTranslation($key, $locale, true);
    }

    /**
     * @param string $key
     * @param string $locale
     * @return mixed
     */
    public function getTranslationWithoutFallback(string $key, string $locale)
    {
        return $this->getTranslation($key, $locale, false);
    }

    /**
     * @param $key
     * @return array
     * @throws AttributeIsNotTranslatable
     */
    public function getTranslations($key) : array
    {
        $this->guardAgainstUntranslatableAttribute($key);

        return json_decode($this->getAttributes()[$key] ?? '' ?: '{}', true);
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

        if ($this->hasSetMutator($key)) {
            $method = 'set' . Str::studly($key) . 'Attribute';
            $value = $this->{$method}($value);
        }

        $translations[$locale] = $value;

        $this->attributes[$key] = $this->asJson($translations);

        event(new TranslationHasBeenSet($this, $key, $locale, $oldValue, $value));

        return $this;
    }

    /**
     * @param string $key
     * @param array $translations
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

    /**
     * @param string $key
     * @return array
     */
    public function getTranslatedLocales(string $key) : array
    {
        return array_keys($this->getTranslations($key));
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isTranslatableAttribute(string $key) : bool
    {
        if (config('madina.use_translatable', true) == false) {
            return false;
        }

        return in_array($key, $this->getTranslatableAttributes());
    }

    /**
     * @param string $key
     * @throws AttributeIsNotTranslatable
     */
    protected function guardAgainstUntranslatableAttribute(string $key)
    {
        if (!$this->isTranslatableAttribute($key)) {
            throw AttributeIsNotTranslatable::make($key, $this);
        }
    }

    /**
     * @param string $key
     * @param string $locale
     * @param bool $useFallbackLocale
     * @return string
     */
    protected function normalizeLocale(string $key, string $locale, bool $useFallbackLocale) : string
    {
        if (in_array($locale, $this->getTranslatedLocales($key))) {
            return $locale;
        }

        if (!$useFallbackLocale) {
            return $locale;
        }

        if (!is_null($fallbackLocale = config('laravel-translatable.fallback_locale'))) {
            return $fallbackLocale;
        }

        return $locale;
    }

    /**
     * @return array
     */
    public function getTranslatableAttributes() : array
    {
        return is_array($this->translatable)
            ? $this->translatable
            : [];
    }

    /**
     * @return array
     */
    public function getCasts() : array
    {
        return array_merge(
            parent::getCasts(),
            array_fill_keys($this->getTranslatableAttributes(), 'array')
        );
    }
}
