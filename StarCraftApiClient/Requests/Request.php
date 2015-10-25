<?php namespace StarCraftApiClient\Requests;

/**
 * @author neun
 * @since  2015-10-25
 */
abstract class Request
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $info;

    /**
     * @var string (JSON)
     */
    protected $response;

    /**
     * @return string $url
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * @return array $info
     */
    public function info()
    {
        return $this->info;
    }

    /**
     * @param array $info
     * @return Request
     */
    public function setInfo($info)
    {
        $this->info = $info;
        return $this;
    }

    /**
     * @return string (JSON) $response
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * @param string (JSON) $response
     * @return Request
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }
}