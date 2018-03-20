<?php

namespace App\Service;


use GuzzleHttp\Client;

class ApiCaller
{
    /**
     * @var \GuzzleHttp\Client $client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://api.github.com','verify' => false]);
    }

    public function call(string $uri, array $query = null)
    {

        $response = $this->client->request('GET', $uri, ['query' => $query]);
        if ($response->getStatusCode() == "200") {
            $body = json_decode($response->getBody()->getContents());

            // Requête sans query: donne directement l'objet
            if (!isset($body->total_count)) {
                return $body;
            }

            // Requête avec query: donne le total_count puis l'objet dans items
            if ($body->total_count > 0) {
                return $body->items;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}