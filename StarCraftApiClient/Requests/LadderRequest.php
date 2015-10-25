<?php namespace StarCraftApiClient\Requests;

use StarCraftApiClient\Client;

/**
 * @author neun
 * @since  2015-10-25
 */
class LadderRequest extends Request
{
    /**
     * LadderRequest constructor.
     * @param Client $client
     * @param int $id
     * @param string $region
     */
    public function __construct($client, $id, $region)
    {
        $url = 'https://%s.api.battle.net/sc2/ladder/%s?locale=%s&apikey=%s';
        $this->url = sprintf($url, $region, $id, $client->language(), $client->apiToken());
    }
}