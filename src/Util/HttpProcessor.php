<?php
namespace ApigeeKongUtil;

use Symfony\Component\HttpClient\HttpClient;

class HttpProcessor {
    public function getData($url, $credentials = false)
    {
        $data = [];
        if ($credentials) {
            $data = [
                'headers' => [
                    "Authorization" => "Basic $credentials"
                ],
            ];
        }
        try {
            $client = HttpClient::create();
            $response = $client->request('GET', $url, $data);
            return $response->toArray();

        } catch (\Exception $e) {
            echo "HttpProcessor->getData Exception - " . $e->getMessage() . PHP_EOL;
        }
    }

    public function deleteData($url)
    {
        try {
            $client = HttpClient::create();
            $response = $client->request('DELETE', $url);

        } catch (\Exception $e) {
            echo "Exception - " . $e->getMessage() . PHP_EOL;
        }
    }

    public function postData($url, $data, $dataType = 'json') {
        if ($dataType == 'json') {
            $data = [
                'json' => $data,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ];
        }
        else {
            $data = [
                'body' => $data
            ];
        }

        try{
            $client = HttpClient::create();
            $response = $client->request('POST', $url, $data);
            if ($response->getStatusCode() == 201 || $response->getStatusCode() == 200) {
                return $response->toArray();
            }
            else {
                return false;
            }
        }
        catch (Exception $exception) {
            echo "Exception - " . $exception->getMessage() . PHP_EOL;
        }
    }
}