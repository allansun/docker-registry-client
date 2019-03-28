<?php

namespace DockerRegistry\Test\Credential;

use DockerRegistry\Credential\AnonymousCredential;
use PHPUnit\Framework\TestCase;

class AnonymousCredentialTest extends TestCase
{

    public function testGetToken()
    {
        $Credential = new AnonymousCredential();
        $this->assertNull($Credential->getToken());

    }
}
