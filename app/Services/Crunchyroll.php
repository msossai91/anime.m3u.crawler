<?php

namespace App\Services;

use App\Repositories\RequestGuzzle;

use Msossai91\Easegex\Easegex;

class Crunchyroll
{
    private $animeList;

    public function __construct()
    {

    }

    public function loadAnimeListFromDB()
    {

    }

    public function crawlerAnimes()
    {
        $result = (new RequestGuzzle())->setUrl('https://www.crunchyroll.com/pt-br/my-hero-academia')->get();

        $titlePattern = '/<h1[^>]*?ellipsis[^>]*?>\s*<span[^>]*?>\s*([^<]*?)\s*<\/span/is';
        if(!$animeTitle = Easegex::regex($titlePattern, $result)->match())
        {
            dd('Couldn\'t find the anime');
        }

        $seasonsPattern = '/<li[^>]*?class=\"\s*season[^>]*?>\s*(?:<a[^>]*?>\s*([^<]*?)<\/a[^>]*?>\s*)?<ul[^>]*?>\s*(.*?)\s*<\/ul/is';
        if(!$seasons = Easegex::regex($seasonsPattern, $result)->matchAll())
        {
            dd('Couldn\'t find seasons');
        }

        $episodesList = [];

        if(count($seasons) === 3)
        {
            foreach($seasons[1] as $key => $value)
            {
                $episodesList[] = [
                    'season'    => $value,
                    'episodes'  => $this->getEpisodesInfos($seasons[2][$key]),
                ];
            }
        }
        elseif(count($seasons) === 2)
        {
            $episodesList[] = [
                'episodes'  => $this->getEpisodesInfos($seasons[1][$key]),
            ];
        }
        else
        {
            dd('Couldn\'t define the seasons matches');
        }

        $this->accessEpisodes($episodesList);

        dd($episodesList);

        file_put_contents('teste.html', $result);
        dd('ok');
    }

    private function getEpisodesInfos($html)
    {
        $listEpisodesPattern = '/<li[^>]*>\s*(.*?)\s*<\/li[^>]*?>\s*<script>[^\{]*?(\{[^\}]*?\})/is';
        if(!$matchlistEpisodes = Easegex::regex($listEpisodesPattern, $html)->matchAll())
        {
            dd('Couldn\'t define the list of episodes');
        }

        $episodes = [];

        foreach($matchlistEpisodes[1] as $key => $value)
        {
            $episode = [];

            $linkImagePattern = '/<a[^>]*?href=\"([^\"]*)\"[^<]*?<img[^>]*?(?:src|data\-thumbnailUrl)=\"([^\"]*)/is';
            if(!$matchLinkImage = Easegex::regex($linkImagePattern, $value)->match())
            {
                dd('Couldn\'t find de link and image');
            }

            $episode = [
                'link' => $matchLinkImage[1],
                'tumb' => $matchLinkImage[2],
            ];

            $jsonObj = json_decode($matchlistEpisodes[2][$key]);

            $episode['name'] = $jsonObj->name;
            $episode['description'] = $jsonObj->description;

            $episodes[] = $episode;
        }

        return $episodes;
    }

    private function accessEpisodes(&$episodesList)
    {
        foreach($episodesList as $seasonKey => $season)
        {
            foreach($season['episodes'] as $episodeKey => $episode)
            {
                $result = (new RequestGuzzle())->setUrl('https://www.crunchyroll.com/' . $episode['link'])->get();

                $this->captureStreamLink($result);
            }
        }
    }

    private function captureStreamLink($html)
    {
        $episodeJsonPattern = '/vilos\.config\.media\s*=\s*(\{.*?\})\;/is';
        if(!$matchJson = Easegex::regex($episodeJsonPattern, $html)->match())
        {
            dd('Couldn\'t find the episode json');
        }

        $jsonObj = json_decode($matchJson[1]);

        dd($jsonObj);

        file_put_contents('teste.html', $html);
        dd('ok');
    }

    public function generateM3uFile()
    {

    }
}