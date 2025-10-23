<?php

namespace Nmc\Validation\Traits;

trait TranslationsTrait
{
    /**
     * @var array<string, string>
     */
    protected $translations = [];

    /**
     * Given $key and $translation to set translation
     */
    public function setTranslation(string $key, string $translation): void
    {
        $this->translations[$key] = $translation;
    }

    /**
     * Given $translations and set multiple translations
     *
     * @param  array<string, string>  $translations
     */
    public function setTranslations(array $translations): void
    {
        $this->translations = array_merge($this->translations, $translations);
    }

    /**
     * Given translation from given $key
     */
    public function getTranslation(string $key): string
    {
        return array_key_exists($key, $this->translations) ? $this->translations[$key] : $key;
    }

    /**
     * Get all $translations
     *
     * @return array<string, string>
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }
}
