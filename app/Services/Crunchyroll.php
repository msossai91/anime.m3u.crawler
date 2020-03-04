<?php

namespace App\Services;

use App\Repositories\RequestGuzzle;

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
        $result = (new RequestGuzzle())->setUrl('https://www.crunchyroll.com/pt-br')->get();

        file_put_contents('teste.html', $result);
        dd('ok');
    }

    public function generateM3uFile()
    {

    }
}