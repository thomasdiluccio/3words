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
        $url = (string) $request->input('url') ?? 'https://www.academia.edu/42919640/Towards_digital_sobriety_report_by_The_Shift_Project_';

        $content = $urlManager->extract($url);
        $language = $urlManager->detectLanguageCode($content);

        return [
            'url' => $url,
            'language' => $language,
            'suggestions' => $urlManager->getSuggestedTriads($url),
        ];
    }
}
