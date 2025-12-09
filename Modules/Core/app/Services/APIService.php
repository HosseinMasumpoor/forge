<?php

namespace Modules\Core\Services;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class APIService
{
    protected PendingRequest $client;
    protected string $baseUrl;
    protected string $apiToken;
    protected array $headers;
    public function __construct()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $this->headers = $headers;

        $this->client = Http::timeout(30)
            ->retry(3, 100)->withHeaders($headers);
    }

    public function config(string $token = null, string $baseUrl = null, array $headers = []): void
    {
        if ($baseUrl) {
            $this->client->baseUrl($baseUrl);
        }

        if ($token) {
            $this->client->withToken($token);
        }

        $this->client->withHeaders(array_merge($headers, $this->headers));
    }

    public function get($url, array $query = [], array $headers = [])
    {
        $this->client->withHeaders(array_merge($headers, $this->headers));
        return $this->client->get($url, $query);
    }

    public function post(string $url, array $data = [], array $headers = [])
    {
        $this->client->withHeaders(array_merge($headers, $this->headers));

        return $this->client->post($url, $data);
    }

    public function put(string $url, $data, array $headers = [])
    {
        $this->client->withHeaders(array_merge($headers, $this->headers));

        try {
            $response = $this->client->put($url, $data);
            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            throw new \Exception($e->getResponse()->getBody());
        } catch (GuzzleException $e) {
            throw new \Exception('Error while putting data: ' . $e->getMessage());
        } catch (ConnectionException $e) {
            throw new \Exception('Error while putting data: ' . $e->getMessage());
        }
    }

    public function delete($url, array $headers = [])
    {
        $this->client->withHeaders(array_merge($headers, $this->headers));

        return $this->client->delete($url);

    }

    public function postMultipart(string $url, array $data, array $headers = [])
    {
        $this->client->withHeaders(array_merge($headers, $this->headers));

        if (isset($data['file']['path']) && is_array($data['file'])) {
            $this->client->attach(
                $data['file']['name'] ?? 'file',
                file_get_contents($data['file']['path']),
                basename($data['file']['path'])
            );
            unset($data['file']);
        }

        return $this->client->post($url, $data);
    }

}
