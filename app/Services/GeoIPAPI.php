<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: GeoIPAPI.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Services;

use Exception;
use Torann\GeoIP\Services\AbstractService;
use Torann\GeoIP\Support\HttpClient;

class GeoIPAPI extends AbstractService
{
    /**
     * Http client instance.
     *
     * @var HttpClient
     */
    protected $client;

    /**
     * The "booting" method of the service.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->client = new HttpClient([
            'base_uri' => 'https://freeipapi.com/',
        ]);
    }

    /**
     * @throws Exception
     */
    public function locate($ip): \Torann\GeoIP\Location
    {
        // Get data from client
        $data = $this->client->get('api/json/'.$ip);

        if ($this->client->getHttpCode() === 429 || $this->client->getErrors() !== null) {
            // rate limit reached. return default template
            return $this->hydrate([
                'ip' => $ip,
                'iso_code' => 'UNKNOWN',
                'country' => 'UNKNOWN',
                'city' => 'UNKNOWN',
                'state' => 'UNKNOWN',
                'state_name' => 'UNKNOWN',
                'postal_code' => 'UNKNOWN',
                'lat' => 'UNKNOWN',
                'lon' => 'UNKNOWN',
                'timezone' => 'UNKNOWN',
                'continent' => 'UNKNOWN',
            ]);
        }

        // Parse body content
        $json = json_decode($data[0]);

        return $this->hydrate([
            'ip' => $ip,
            'iso_code' => $json->continentCode,
            'country' => $json->countryName,
            'city' => $json->cityName,
            'state' => $json->countryName,
            'state_name' => $json->countryName,
            'postal_code' => $json->zipCode,
            'lat' => $json->latitude,
            'lon' => $json->longitude,
            'timezone' => $json->timeZone,
            'continent' => $json->continent,
        ]);

    }
}
