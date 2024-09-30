<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\History;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\DomCrawler\Crawler;

class Scrape extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scrape';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected const DOMAIN = 'https://store.playstation.com';
    protected const REGIONS = ['en-us', 'en-gb', 'de-de', 'ja-jp'];
    protected const MAX_PAGES = 2;

    /**
     * Execute the console command.
     */
    #[NoReturn] public function handle(): void
    {
        $games = collect();

        foreach (static::REGIONS as $region) {
            $page = 1;
            $index = 1;

            do {
                $crawler = new Crawler(Http::timeout(60)->get(static::DOMAIN . "/{$region}/pages/browse/{$page}")->body());

                $this->setGameFields($crawler, $games, $region, $index);

                $index = $index + 23;

                $page++;

            } while ($page <= static::MAX_PAGES);
        }

        foreach ($games as $game) {
            $gameDB = Game::updateOrCreate(
                ['name' => $game['name']],
                [
                    'link' => $game['link'],
                    'genres' => $game['genres'],
                    'release_date' => $game['releaseData'],
                    'publisher' => $game['publisher'],
                    'reviews' => $game['reviews'],
                    'rating' => $game['rating'],
                    'price' => $game['price'],
                    'positions' => $game['positions'],
                ]
            );

            foreach ($game['positions'] as $region => $position) {
                History::create([
                    'game_id' => $gameDB->id,
                    'region' => $region,
                    'position' => $position,
                    'date' => now(),
                ]);
            }

        }

        dd($games);
    }

    protected function setGameFields(Crawler $crawler, Collection $games, string $region, int $index): int
    {
        $crawler->filter('.psw-grid-list li')->each(function (Crawler $liNode) use ($games, $region, &$index) {
            $link = $liNode->filter('a')->first()->attr('href');

            $existingGame = $games->filter(function ($game) use ($link) {
                if (Str::of($game['link'])->contains(Str::after($link, '/concept/'))) {
                    return $game;
                }

                return null;
            })->first();

            if ($existingGame) {
                $index++;
                $games = $games->transform(function ($game) use ($region, $index, $link) {
                    if (Str::of($game['link'])->contains(Str::after($link, '/concept/'))) {
                        $game['positions'][$region] = $index;
                    }

                    return $game;
                });

                return;
            }

            $gameView = new Crawler(Http::get(static::DOMAIN . $link)->body());

            $genres = $gameView->filter('dd[data-qa="gameInfo#releaseInformation#genre-value"] span')->first()->text();
            $releaseDate = $gameView->filter('dd[data-qa="gameInfo#releaseInformation#releaseDate-value"]')->first()->text();
            $publisher = $gameView->filter('dd[data-qa="gameInfo#releaseInformation#publisher-value"]')->first()->text();

            $reviewsNode = $gameView->filter('span[data-qa="mfe-star-rating#overall-rating#total-ratings"]')->first();
            $reviews = $reviewsNode->count() > 0 ? $reviewsNode->text() : '-';

            $ratingNode = $gameView->filter('span[data-qa="mfe-star-rating#overall-rating#average-rating"]')->first();
            $rating = $ratingNode->count() > 0 ? $ratingNode->text() : '-';

            $priceNode = $gameView->filter('span[data-qa="mfeCtaMain#offer0#finalPrice"]')->first();
            $price = $priceNode->count() > 0 ? $priceNode->text() : '-';

            $initialPositions = [];
            foreach (static::REGIONS as $reg) {
                if (!isset($initialPositions[$reg])) {
                    $initialPositions[$reg] = '-';
                }
            }

            $initialPositions[$region] = $index;

            $games->push([
                'name' => $liNode->text(),
                'link' => $link,
                'genres' => $genres,
                'releaseData' => $this->parseReleaseDate($releaseDate),
                'publisher' => $publisher,
                'positions' => $initialPositions,
                'reviews' => $reviews,
                'rating' => $rating,
                'price' => $price,
            ]);

            $index++;
        });

        return $index;
    }


    function parseReleaseDate($releaseDate): \Illuminate\Support\Carbon|string
    {
        $formats = [
            'd.m.Y',
            'd/m/Y',
            'Y.m.d',
            'Y/m/d',
            'd-m-Y',
            'Y-m-d'
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $releaseDate)->format('d.m.Y');
            } catch (InvalidFormatException $e) {
            }
        }

        return now();
    }
}
