<?php

namespace App\Managers;

use App\Exceptions\InvalidUrlException;
use App\Models\Url;
use DonatelloZa\RakePlus\RakePlus;
use Goutte\Client;
use LanguageDetection\Language;

class UrlManager
{
    /** @var Client */
    public $client;

    /** @var Language */
    public $language;

    public function __construct(
        Client $client,
        Language $language
    ) {
        $this->client = $client;
        $this->language = $language;
    }

    /**
     * @return array<int, string>
     */
    public function getSuggestedTriads(string $url): array
    {
        $this->validateUrl($url);

        $content = $this->extract($url);

        $languageCode = $this->detectLanguageCode($content);
        $locale = $this->getLocale($languageCode);

        $rake = RakePlus::create($content, $locale, 3);
        $phrasesScores = $rake->sortByScore('desc')->scores();

        $keywords = [];
        foreach ($phrasesScores as $phrase => $score) {
            if ($score <= 3 && count($keywords)) {
                continue;
            }

            $words = explode(' ', (string) $phrase);
            $keywords = array_merge($keywords, $words);
        }

        $keywords = array_unique($keywords);

        $triads = [];
        for ($i = 0; $i < 5; $i++) {
            $triad = $this->getRandomTriad($keywords);
            $existingUrl = Url::where('triad', $triad)->first();
            if (!$existingUrl) {
                $triads[] = $triad;
            }
        }

        return $triads;
    }

    public function detectLanguageCode(string $content): string
    {
        $detection = $this->language
            ->detect($content)
            ->bestResults()
            ->close();

        return (string) array_key_first($detection) ?? '';
    }

    /**
     * Crawl the URL and extract content data
     */
    public function extract(string $url): string
    {
        $data = [];

        $crawler = $this->client->request('GET', $url);
        $crawler
            ->filter('title, h1, h2')
            ->each(function ($node) use (&$data): void {
                $data[] = $node->text();
            });

        return implode(' ', $data);
    }

    private function validateUrl(string $url): void
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidUrlException();
        }
    }

    private function getLocale(string $languageCode): string
    {
        switch ($languageCode) {
            case 'ar':
                return 'ar_AE';
            case 'de':
                return 'de_DE';
            case 'es':
                return 'es_AR';
            case 'fr':
                return 'fr_FR';
            case 'pl':
                return 'pl_PL';
            case 'pt':
                return 'pt_PT';
            case 'ru':
                return 'ru_RU';
            case 'en':
            default:
                return 'en_US';
        }
    }

    /**
     * @param  array<int, string> $keywords
     */
    private function getRandomTriad(array $keywords): string
    {
        shuffle($keywords);

        return sprintf(
            '%s.%s.%s',
            $keywords[0] ?? '',
            $keywords[1] ?? '',
            $keywords[2] ?? ''
        );
    }
}
