<?php namespace StarCraftApiClient\Requests;

use StarCraftApiClient\Client;

/**
 * @author neun
 * @since  2015-10-25
 */
class ProfileRequest extends Request
{
    /**
     * ProfileRequest constructor.
     * @param Client $client
     * @param string $type
     * @param int $id
     * @param string $name
     * @param string $region
     */
    public function __construct($client, $type, $id, $name, $region)
    {
        $url = 'https://%s.api.battle.net/sc2/profile/%s/1/%s/%s?locale=%s&apikey=%s';

        if (in_array($type, ['ladders', 'matches'])) {
            $this->url =
                sprintf($url, $region, $id, $name, $type, $client->language(), $client->apiToken());
        } else {
            $this->url =
                sprintf($url, $region, $id, $name, null, $client->language(), $client->apiToken());
        }
    }
}