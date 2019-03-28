<?php

namespace DockerRegistry\Test\API;

use DockerRegistry\API\VersionCheck;
use PHPUnit\Framework\TestCase;

class AbstractAPITest extends TestCase
{
    protected $uriBackup;

    public function testSetUri()
    {
        $API = new VersionCheck();

        $fixture = 'http://dummy.test';
        $API->setUri($fixture);
        $this->assertEquals($fixture, $API->getUri());
    }

    protected function setUp(): void
    {
        $this->uriBackup = (new VersionCheck())->getUri();
    }

    protected function tearDown(): void
    {
        (new VersionCheck())->setUri($this->uriBackup);
    }
}
