<?php namespace StarCraftApiClient;

use StarCraftApiClient\Requests\DataRequest;
use StarCraftApiClient\Requests\LadderRequest;
use StarCraftApiClient\Requests\ProfileRequest;

/**
 * Sloppy way to perform several requests to the battle.net StarCraft Api
 * in one go. Once you added one or more requests, start pulling from
 * battle.net via Client->run(). Individual results can be accessed via
 * a dot-style path e.g. [basetype.id.type].
 *
 * After requesting all data for one player like:
 * Client->addAllProfileRequests(123321, 'CoolGuy', 'eu') ...
 * ... Client->get('profile.123321.ladders') will give you just the ladders,
 * while Client->get('profile.123321') will return an array containing
 * profil, matches and ladders for this player.
 * Hence Client->get('profile') will give you all the requested
 * profile-type results as an array.
 *
 * The actual request object for individual requests is accessible via
 * Client->getRequest(...). To access e.g. CURL info of a request
 * Client->getRequest('profile.123321.ladders')->info()
 * will give you just that.
 *
 * FYI: You should keep the number of requests to perform within
 * a reasonable level. See https://dev.battle.net/ for details.
 *
 * @author neun
 * @since  2015-10-25
 */
class Client
{
    /**
     * @var string
     */
    private $apiToken;

    /**
     * @var string
     */
    private $language;

    /**
     * @var float
     */
    private $runtime;

    /**
     * @var array
     */
    private $curlOptions = [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_CONNECTTIMEOUT => 9,
        CURLOPT_FOLLOWLOCATION => 1
    ];

    /**
     * @var Requests\Request[]
     */
    private $requests;

    /**
     * @var array
     */
    private $results;

    public function __construct($apiToken, $language = 'en_GB')
    {
        $this->apiToken = $apiToken;
        $this->language = $language;
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $region
     */
    public function addAllProfileRequests($id, $name, $region)
    {
        $this->addProfileRequest($id, $name, $region);
        $this->addProfileLaddersRequest($id, $name, $region);
        $this->addProfileMatchesRequest($id, $name, $region);
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $region
     */
    public function addProfileRequest($id, $name, $region)
    {
        $this->requests['profile.'.$id.'.profile'] =
            new ProfileRequest($this, 'profile', $id, $name, $region);
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $region
     */
    public function addProfileLaddersRequest($id, $name, $region)
    {
        $this->requests['profile.'.$id.'.ladders'] =
            new ProfileRequest($this, 'ladders', $id, $name, $region);
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $region
     */
    public function addProfileMatchesRequest($id, $name, $region)
    {
        $this->requests['profile.'.$id.'.matches'] =
            new ProfileRequest($this, 'matches', $id, $name, $region);
    }

    /**
     * @param int $id
     * @param string $region
     */
    public function addLadderRequest($id, $region)
    {
        $this->requests['ladder.'.$id] =
            new LadderRequest($this, $id, $region);
    }

    /**
     * @param string $region
     */
    public function addAchievementsRequest($region)
    {
        $this->requests['data.achievements'] =
            new DataRequest($this, 'achievements', $region);
    }

    /**
     * @param string $region
     */
    public function addRewardsRequest($region)
    {
        $this->requests['data.rewards'] =
            new DataRequest($this, 'rewards', $region);
    }

    /**
     * @return void
     */
    public function run()
    {
        $start = -microtime(1);
        $mh = curl_multi_init();

        $chs = $this->hydrateCurlHandles($mh);
        $this->performCurlRequests($mh);
        $this->fetchCurlRequestData($chs, $mh);

        curl_multi_close($mh);
        $this->runtime = $start+microtime(1);
    }

    /**
     * @param resource $mh
     * @return resource[] $chs
     */
    private function hydrateCurlHandles($mh)
    {
        /** @var Requests\Request $request */
        foreach ($this->requests as $id => $request) {
            $chs[$id] = curl_init($request->url());
            curl_setopt_array($chs[$id], $this->curlOptions);
            curl_multi_add_handle($mh, $chs[$id]);
        }

        return isset($chs) ? $chs : [];
    }

    /**
     * @param resource $mh
     */
    private function performCurlRequests($mh)
    {
        do {
            usleep(11111);
            curl_multi_exec($mh, $active);
        } while ($active > 0);
    }

    /**
     * @param resource[] $chs
     * @param resource $mh
     */
    private function fetchCurlRequestData($chs, $mh)
    {
        foreach ($chs as $id => $ch) {
            $path = explode('.', $id);
            $response = curl_multi_getcontent($ch);
            $this->requests[$id]->setInfo(curl_getinfo($ch))->setResponse($response);

            $result = json_decode($response);
            if (isset($path[2])) {
                $this->results[$path[0]][$path[1]][$path[2]] = $result;
            } elseif (isset($path[1])) {
                $this->results[$path[0]][$path[1]] = $result;
            } else {
                $this->results[$path[0]] = $result;
            }

            curl_multi_remove_handle($mh, $ch);
        }
    }

    /**
     * Return indiviual results, collections of results
     * or all results entirely.
     *
     * @param string $path
     * @return Object
     */
    public function get($path = null)
    {
        if (null === $path) {
            $result = $this->results;
        } else {
            foreach (explode('.', $path) as $chunk) {
                $result = isset($result) ? $result[$chunk] : $this->results[$chunk];
            }
        }

        return isset($result) ? $result : null;
    }

    /**
     * Return the individual result objects, containing the
     * plain response, connection info etc.
     *
     * @param $path
     * @return Requests\Request[]|
     */
    public function getRequests($path = null)
    {
        if (null === $path) {
            $requests = $this->requests;
        } else {
            $requests[] = isset($this->requests[$path]) ? $this->requests[$path] : null;
        }

        return $requests;
    }

    /**
     * @return string $apiToken
     */
    public function apiToken()
    {
        return $this->apiToken;
    }

    /**
     * @return string $language
     */
    public function language()
    {
        return $this->language;
    }

    /**
     * @return float $runtime
     */
    public function runtime()
    {
        return $this->runtime;
    }
}
