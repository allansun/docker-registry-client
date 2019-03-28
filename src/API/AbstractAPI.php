<?php


namespace DockerRegistry\API;


use DockerRegistry\Client;

abstract class AbstractAPI
{
    /** @var string */
    static protected $uri = 'https://index.docker.io';

    /** @var Client */
    protected $Client;

    /**
     * AbstractAPI constructor.
     */
    public function __construct()
    {
        $this->Client = Client::getInstance();
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return static::$uri;
    }

    /**
     * @param string $uri
     *
     * @return AbstractAPI
     */
    public function setUri($uri)
    {
        static::$uri = $uri;

        return $this;
    }
}