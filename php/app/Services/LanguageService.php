<?php

namespace App\Services;

use App\Config\Config;

class LanguageService
{
    public function getLanguages(): array
    {
        $languageFiles = scandir(__DIR__ . '/../../lang');
        $languages = [];
        foreach ($languageFiles as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $fileContent = file_get_contents(__DIR__ . '/../../lang/' . $file);
            $language = json_decode($fileContent, true);
            $languages[$language['__code__']] = $language;
        }

        return $languages;
    }
}
