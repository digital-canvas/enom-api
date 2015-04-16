<?php
namespace DigitalCanvas\Enom;

use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface as Response;

abstract class Enom
{

    const URL = 'http://reseller.enom.com/interface.asp';

    const URL_TEST = 'https://resellertest.enom.com/interface.asp';

    const PRODUCTION = true;

    const TEST = false;

    /**
     * @var string
     */
    protected $uid;

    /**
     * @var string
     */
    protected $pw;

    /**
     * @var bool
     */
    protected $mode = true;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    private static $services = [
      'WebsiteBuilder',
      'DNS',
      'Email',
      'Domain'
    ];

    /**
     * constructor
     *
     * @param string $uid
     * @param string $pw
     * @param Client $client
     */
    public function __construct($uid, $pw, Client $client)
    {
        $this->uid = $uid;
        $this->pw = $pw;
        $this->setHttpClient($client);
    }

    /**
     * @param bool $mode
     * @return self
     */
    public function setMode($mode = true)
    {
        $this->mode = (bool)$mode;

        return $this;
    }

    /**
     * @param Client $client
     * @return self
     */
    public function setHttpClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->client;
    }

    /**
     * @param string $service
     * @param string $uid
     * @param string $pw
     * @param Client $client
     * @return mixed
     */
    public static function factory($service, $uid, $pw, Client $client)
    {
        if (!in_array($service, static::$services)) {
            throw new \InvalidArgumentException("Invalid service");
        }

        $class = "\\DigitalCanvas\\Enom\\Service\\" . $service;

        return new $class($uid, $pw, $client);
    }

    /**
     * @param array $params
     * @return Response
     */
    protected function sendRequest(array $params = array())
    {
        $url = ($this->mode) ? static::URL : static::URL_TEST;
        // Add Credentials to Params
        $params = array_merge($params, [
          'UID' => $this->uid,
          'PW' => $this->pw,
          'ResponseType' => 'XML'
        ]);

        return $this->client->post($url, [
          'query' => $params
        ]);

    }
}
