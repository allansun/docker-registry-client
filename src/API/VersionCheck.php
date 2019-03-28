<?php


namespace DockerRegistry\API;


class VersionCheck extends AbstractAPI
{

    /**
     * @param string $version
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($version = 'v2')
    {
        $response = $this->Client->request('GET', $this->getUri() . "/${version}/");

        return in_array($response->getStatusCode(), ['200', '401']);
    }
}