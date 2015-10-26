<?php namespace StarCraftApiClient\Requests;

use StarCraftApiClient\Client;

/**
 * @author neun
 * @since  2015-10-25
 */
class DataRequest extends Request
{

    /**
     * DataRequest constructor.
     * @param Client $client
     * @param string $type
     * @param $region
     */
    public function __construct($client, $type, $region)
    {
        $url = 'https://%s.api.battle.net/sc2/data/%s?locale=%s&apikey=%s';

        if (in_array($type, ['achievements', 'rewards'])) {
            $this->url =
                sprintf($url, $region, $type, $client->language(), $client->apiToken());
        }
    }
}
