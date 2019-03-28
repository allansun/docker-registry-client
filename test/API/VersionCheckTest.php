<?php

namespace DockerRegistryTest\API;

use DockerRegistry\API\VersionCheck;
use DockerRegistry\Client;
use DockerRegistry\Credential\BasicCredential;
use DockerRegistryTest\Fixture\DockerCredential;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class VersionCheckTest extends TestCase
{
    public function testGet()
    {
        $API = new VersionCheck();

        $this->assertTrue($API->get('v2'));
    }

    protected function setUp(): void
    {
        Client::configure(
            new BasicCredential(DockerCredential::USERNAME, DockerCredential::PASSWORD),
            null,
            new Logger('DockerRegistry', [new StreamHandler('php://stdout')]));
    }
}
