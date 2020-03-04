<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Crunchyroll;

class CrawlerController extends Controller
{
    public function __invoke()
    {
        (new Crunchyroll())->crawlerAnimes();
    }
}
