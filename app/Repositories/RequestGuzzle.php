<?php

namespace App\Repositories;

class RequestGuzzle
{
    private $client;
    private $url;
    private $params;

    public function __construct()
    {
        $this->client = null;
        $this->url = null;
    }

    private function initClient()
    {
        $this->client = new \GuzzleHttp\Client([
            // Define se vai mostrar mensagens de Debug nos requests
            'debug'         => false,
            // Desativa verificação de certificados SSL
            'verify'        => false,
            // Armazena os cookies
            //'cookies'       => $jar,
            'cookies' => true,
            // Timeout de leitura
            'read_timeout'  => 120,
            // Timeout de requisição
            'timeout'       => 120,
            // Timeout de conexão com o site
            'connect_timeout' => 120,
            // Segue redirects do site
            'allow_redirects' => [
                'max'             => 10,
                'strict'          => true,
                'referer'         => true,
                'track_redirects' => true,
            ],
        ]);
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    public function get()
    {
        if($this->client == null)
        {
            $this->initClient();
        }

        $url = $this->url;

        if($this->params !== null)
        {
            $url .= '?' . http_build_query($this->params);
        }

        $response = $this->client->request('GET', $url);

        return $response->getBody()->getContents();
    }
}