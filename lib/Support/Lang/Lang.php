<?php

namespace Lib\Support\Lang;

use Lib\Exception\CustomException;
use Lib\Support\File;

/**
 * Class Lang
 * 
 * Provides functionality for working with language files.
 * 
 * @CodeError 24
 */
class Lang
{
    /**
     * The current language.
     * 
     * @var string $lang
     */
    protected static $lang;

    /**
     * The translations.
     * 
     * @var array $translations
     */
    protected static $translations = [];

    /**
     * The singleton instance.
     * 
     * @var Lang $instance
     */
    protected static $instance = null;

    /**
     * Constructor. Loads the translations.
     */
    public function __construct()
    {
        self::$lang = config('app.locale', 'en');
        $this->loadTranslations();
    }

    /**
     * Get the singleton instance.
     * 
     * @return Lang
     */
    public static function getInstance(): self
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get the translated string.
     * 
     * @param string $key
     * @param array  $replace
     * 
     * @return string
     */
    public static function lang(string $key, array $replace = []): string
    {
        self::getInstance();

        $translated = self::$translations[$key] ?? $key;

        if (!empty($replace)) {
            $translated = str_replace(array_keys($replace), array_values($replace), $translated);
        }

        $translated = str_replace(':', '', $translated);

        return $translated;
    }

    /**
     * Load the translations.
     * 
     * @throws \Exception
     * @throws CustomException
     * 
     * @return void
     */
    protected function loadTranslations(): void
    {
        try {
            if (!empty(self::$translations)) {
                return;
            }

            if (empty(self::$lang)) {
                throw new \Exception('Lang is not configured in config/app.php.');
            }

            $baseLang = base_path() . 'lang/' . self::$lang;
            $libLang = lib_path() . '/Support/Lang/Langs/' . self::$lang;

            $publicTranslations = $this->loadTranslationsFromPath($baseLang);
            $libTranslations = $this->loadTranslationsFromPath($libLang);

            if (empty($publicTranslations) && empty($libTranslations)) {
                throw new \Exception('Lang file not found in ' . self::$lang . ' folder.' . PHP_EOL . 'File path: ' . $libLang . PHP_EOL . 'File path: ' . $baseLang);
            }

            self::$translations = array_merge($publicTranslations, $libTranslations);
        } catch (\Exception $e) {
            throw new CustomException(2401, 'Lang file not found', $e->getMessage());
        }
    }

    /**
     * Load translations from path.
     * 
     * @param string $path
     * 
     * @return array
     */
    protected function loadTranslationsFromPath(string $path): array
    {
        try {
            $translations = [];

            if (File::isDirectory($path)) {
                $files = glob($path . '/*.php');

                foreach ($files as $file) {
                    $translation = include $file;
                    $translations = array_merge($translations, $translation);
                }
            }

            return $translations;
        } catch (\Exception $e) {
            throw new CustomException(2402, 'Lang file not found', $e->getMessage());
        }
    }
}
