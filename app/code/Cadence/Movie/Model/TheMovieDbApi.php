<?php
namespace Cadence\Movie\Model;

use GuzzleHttp\Client;
use Cadence\Movie\Helper\Config;

class TheMovieDbApi
{
    const BASE_API_URL = 'https://api.themoviedb.org/3/movie/';

    /**
     * @var Config
     */
    public $config;

    /**
     * __construct
     *
     * @param Config $Config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Get Popular Movies
     *
     * @return mixed
     */
    public function getPopularMovies()
    {
        $popular = $this->callApi('popular');
        return $popular; // only return the 1st page of results
    }

    /**
     * Get Movie Details
     *
     * @param int|string $movieId
     * @return array
     */
    public function getMovieDetails($movieId)
    {
        $details = $this->callApi((string)$movieId);

        $genres = array_map(function($item) {
            return $item->name;
        }, $details->genres);

        return [
            'genre' => implode(', ', $genres),
            'year' => date("Y", strtotime($details->release_date)),
            'vote_average' => $details->vote_average,
        ];
    }

    /**
     * Get Movie Credits
     *
     * @param int|string $movieId
     * @return array
     */
    public function getMovieCredits($movieId)
    {
        $credits = $this->callApi((string)$movieId . '/credits');

        $actors = array_map(function ($item) {
            return $item->name;
        }, $credits->cast);
        
        $staff = array_reduce($credits->crew, function ($carry, $item) {
            if ($item->job == "Producer") {
                $carry['producer'][] = $item->name;
            } elseif ($item->job == "Director") {
                $carry['director'][] = $item->name;
            }
            return $carry;
        }, [
            'producer' => [],
            'director' => [],
        ]);

        return array_map(function ($item) {
            return implode(', ', $item);
        }, array_merge(['actors' => $actors], $staff));
    }

    /**
     * Call Api
     *
     * @param string $apiEndpoint
     * @return mixed
     * @throws RequestException
     */
    public function callApi($apiEndpoint)
    {
        $accessToken = $this->config->getApiKey();

        $client = new Client();

        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ];

        $data = [];

        // Make the request
        try {
            $response = $client->get(self::BASE_API_URL . $apiEndpoint, [
                'headers' => $headers,
            ]);

            $data = json_decode($response->getBody());
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw $e;
        }

        return $data;
    }
}
