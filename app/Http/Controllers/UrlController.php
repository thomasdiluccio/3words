<?php

namespace App\Http\Controllers;

use App\Managers\UrlManager;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    /**
     * @return array{url: string, language: string, suggestions: array<int, string>}
     */
    public function suggest(Request $request, UrlManager $urlManager): array
    {
        $url = (string) $request->input('url');

        return $urlManager->getSuggestionData($url);
    }
}
