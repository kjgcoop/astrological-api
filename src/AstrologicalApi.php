<?php

namespace Kjgcoop\AstronomicalApi;

use GuzzleHttp\Client;
use Kjgcoop\CliLogger\CliLogger;

class AstronomicalApi
{
    private $dateFormat = 'Y-m-d';

    public function __construct(array $config, CliLogger $logger) {
        $this->client = new Client();

        $this->config = $config;
        $this->logger = $logger;

        $this->dateFormat = $this->config['date_format'];

        $this->base = 'https://aa.usno.navy.mil/api/';

        // Debugging preferences
        $this->echo = $config['echo'] ?? false;
        $this->log = $config['log'] ?? false;
    }

    public function request($uri, $method, $params) {

        $this->logger->makeItSo('Sending '.$method.' request to '.$uri);

        try {
            $response = $this->client->request($method, $uri, [
                'query' => $params,
            ]);

            $contents = $response->getBody()->getContents();

            return is_string($contents) ? json_decode($contents) : '';

        } catch (\Exception $e) {
            $this->logger->makeItSo('Problem making '.$method.' request to '.$uri.': '.$e->getMessage());
            throw new \Exception('Problem getting data from '.$uri);
        }
    }

    public function getMoonPhase(\DateTime $dt, string $coords) {
        $uri = $this->base.'rstt/oneday';

        $params = [
            'date' => $dt->format($this->config['date_format']),
            'coords' => $this->config['coords_for_moon']
        ];

        $result = $this->request($uri, 'GET', $params);
        $this->logger->makeItSo('Results of GET request to '.$uri);
        $this->logger->makeItSo($result);

        return $result;
    }

}