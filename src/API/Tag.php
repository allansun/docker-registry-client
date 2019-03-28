<?php


namespace DockerRegistry\API;


class Tag extends AbstractAPI
{
    /**
     * @param      $name
     * @param null $n
     * @param null $last
     *
     * @return string
     * @throws \Exception
     */
    public function list($name, $n = null, $last = null)
    {
        return (string)$this->Client->request('GET', $this->getUri() . "/v2/${name}/tags/list", [
            'query' => [
                'n'    => $n,
                'last' => $last
            ]
        ])->getBody();
    }
}