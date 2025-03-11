<?php

namespace App\Controllers;

use App\Services\LanguageService;

class LangController extends Controller
{
    private LanguageService $languageService;

    public function __construct()
    {
        parent::__construct();
        $this->languageService = new LanguageService();
    }

    public function index(): array
    {
        $languages = $this->languageService->getLanguages();

        return $languages;
    }
}
