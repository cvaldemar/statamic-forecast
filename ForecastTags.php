<?php

namespace Statamic\Addons\Forecast;

use Lvht\GeoHash;
use Statamic\Extend\Tags;

class ForecastTags extends Tags
{
    /**
     * Latitude for current request.
     */
    protected $lat;

    /**
     * Longitude for current request.
     */
    protected $lng;

    /**
     * Calculated geo hash for current request.
     */
    protected $geoHash;

    /**
     * The cache prefix.
     */
    protected $cacheKeyPrefix = 'forecast';

    /**
     * The base URL for the Dark Sky API.
     */
    protected $baseApiUrl = 'https://api.darksky.net/forecast';

    /**
     * The {{ forecast }} tag
     *
     * @return array
     */
    public function index()
    {
        $this->lat = $this->getFloat('lat');
        $this->lng = $this->getFloat('lng');
        $this->block = $this->get('block');

        // Calculate the geohash for this location (used for caching)
        $this->setGeoHash();

        // Get the forecast
        return $this->getForecast();
    }

    /**
     * Get the forecast for a location
     *
     * @param $lat
     * @param $lng
     * @return array
     */
    protected function getForecast()
    {
        if ($this->cache->exists($this->getCacheKey())) {
            $data = $this->cache->get($this->getCacheKey());
        } else {
            $data = $this->sendApiRequest();

            $this->cache->put(
                $this->getCacheKey(),
                $data,
                $this->getConfig('cache_mins')
            );
        }

        return json_decode($data, true);
    }

    /**
     * Get the API key from configuration.
     *
     * @return string
     */
    protected function getApiKey()
    {
        $apiKey = $this->getConfig('api_key', false);

        if (! $apiKey) {
            throw new \Exception('Could not find API key in parameter or settings.');
        }

        return $apiKey;
    }

    protected function getApiUrl()
    {
        // Build query string from parameters
        $qs = http_build_query([
            'lang' => $this->get('lang'),
            'units' => $this->get('units')
        ]);

        // Combine everything to create the URL
        return sprintf(
            '%s/%s/%s,%s?%s',
            $this->baseApiUrl,
            $this->getApiKey(),
            $this->lat,
            $this->lng,
            $qs
        );
    }

    /**
     * Execute API request on Dark Sky.
     *
     * @param string $url
     * @return string
     */
    protected function sendApiRequest()
    {
        $url = $this->getApiUrl();

        return file_get_contents($url);
    }

    /**
     * Get the cache key for current request.
     *
     * @return string
     */
    protected function getCacheKey()
    {
        return sprintf(
            '%s.%s.%s',
            $this->cacheKeyPrefix,
            $this->geoHash,
            $this->get('block')
        );
    }

    /**
     * Calculate and set the geoHash for request.
     *
     * @return void
     */
    protected function setGeoHash()
    {
        $this->geoHash = GeoHash::encode($this->lat, $this->lng);
    }
}
