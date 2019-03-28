<?php

namespace DockerRegistry\Test\Credential;

use DockerRegistry\Credential\BasicCredential;
use DockerRegistryTest\Fixture\DockerCredential;
use PHPUnit\Framework\TestCase;

class BasicCredentialTest extends TestCase
{

    public function testGetToken()
    {
        $Credential = new BasicCredential(DockerCredential::USERNAME, DockerCredential::PASSWORD);
        $this->assertNotNull($Credential->getToken());
    }
}
