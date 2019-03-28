<?php

namespace DockerRegistryTest\API;

use DockerRegistry\API\Tag;
use DockerRegistry\Client;
use DockerRegistry\Credential\BasicCredential;
use DockerRegistryTest\Fixture\DockerCredential;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    public function testList()
    {
        $API      = new Tag();
        $response = $API->list('library/nginx');
        $this->assertTrue(is_array(json_decode($response, true)));

        $response = $API->list('birdsystem/docker-service-app');
        $this->assertTrue(is_array(json_decode($response, true)));
    }

    protected function setUp(): void
    {
        Client::configure(
            new BasicCredential(DockerCredential::USERNAME, DockerCredential::PASSWORD),
            null,
            new Logger('DockerRegistry', [new StreamHandler('php://stdout')]));
    }
}
