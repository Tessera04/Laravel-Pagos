<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait ConsumesExternalServices{
    public function makeRequest($method, $requestUrl, $queryParams = [], $formParams = [], $headers = [], $isJsonRequest = false)
    {
        $client = new Client([
            'base_uri' => $this->getBaseUri(),
        ]);

        $response = $client->request($method, $requestUrl, [
            $isJsonRequest ? 'json' : 'form_params' => $formParams,
            'query' => $queryParams,
            'headers' => $headers
        ]);

        $response = $response->getBody()->getContents();

        return $response;
    }
}

?>