<?php


namespace DockerRegistry\Credential;


class AnonymousCredential extends AbstractCredential
{
    public function getToken()
    {
        return null;
    }

}