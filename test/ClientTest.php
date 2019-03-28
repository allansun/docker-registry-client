<?php

namespace DockerRegistry\Test;

use DockerRegistry\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    protected $backupStaticAttributesBlacklist = [
        Client::class => ['instance']
    ];

    public function testGetInstance()
    {
        $Client = Client::getInstance();
        $this->assertInstanceOf(Client::class, $Client);

    }

    public function testRequest()
    {
        Client::configure(null, new \GuzzleHttp\Client([
            'handler' => HandlerStack::create(
                new MockHandler([
                    new Response(200, ['X-Foo' => 'Bar'])
                ])
            )
        ]));
        $Client = Client::getInstance();
        $this->assertTrue($Client->request('GET', '/')->hasHeader('X-Foo'));

    }
}
