<?php


namespace DockerRegistry;


use DockerRegistry\Credential\AnonymousCredential;
use DockerRegistry\Credential\BasicCredential;
use DockerRegistry\Credential\CredentialInterface;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Psr7;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

class Client
{
    /** @var Client */
    static private $instance;
    /** @var Guzzle */
    private $Guzzle;
    /** @var CredentialInterface|BasicCredential */
    private $Credential;

    /**
     * Client constructor.
     *
     * @param CredentialInterface|null $Credential
     * @param Guzzle|null              $Guzzle
     * @param LoggerInterface|null     $Logger
     */
    private function __construct(
        CredentialInterface $Credential = null,
        Guzzle $Guzzle = null,
        LoggerInterface $Logger = null
    ) {
        $this->Credential = $Credential ?: new AnonymousCredential();

        $HandlerStack = $this->initialiseGuzzleHandler($this->Credential, $Guzzle, $Logger);

        $options = [
            'http_errors' => false,
            'handler'     => $HandlerStack
        ];

        if ($Guzzle) {
            $options += $Guzzle->getConfig();
        }

        $this->Guzzle = new Guzzle($options);
    }

    /**
     * @param CredentialInterface|null $Credential
     * @param Guzzle|null              $Guzzle
     * @param LoggerInterface|null     $Logger
     *
     * @return \GuzzleHttp\HandlerStack
     */
    private function initialiseGuzzleHandler(
        CredentialInterface $Credential = null,
        Guzzle $Guzzle = null,
        LoggerInterface $Logger = null
    ) {
        $HandlerStack = $Guzzle ? $Guzzle->getConfig('handler') : \GuzzleHttp\HandlerStack::create();

        // Prepare Logger
        $Logger = $Logger ?: new NullLogger();
        $HandlerStack->push(
            \GuzzleHttp\Middleware::log(
                $Logger,
                new \GuzzleHttp\MessageFormatter('{response}'),
                LogLevel::DEBUG
            )
        );
        $HandlerStack->push(
            \GuzzleHttp\Middleware::log(
                $Logger,
                new \GuzzleHttp\MessageFormatter('{request}'),
                LogLevel::DEBUG
            )
        );

        // Prepare Authentication header
        $HandlerStack->push(
            function (callable $handler) use ($Credential) {
                return function (RequestInterface $request, array $options) use ($handler, $Credential) {
                    $modify = [];
                    if ($Credential && $Credential->getToken()) {
                        $modify['set_headers']['Authorization'] = 'Bearer ' . $Credential->getToken();
                    }

                    return $handler(Psr7\modify_request($request, $modify), $options);
                };
            },
            'addAuthenticationHeader'
        );

        return $HandlerStack;
    }

    /**
     * @return Client
     * @throws \Exception
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::configure();
        }

        return static::$instance;
    }

    /**
     * @param CredentialInterface|null $Credential
     * @param Guzzle|null              $Guzzle
     * @param LoggerInterface|null     $Logger
     */
    public static function configure(
        CredentialInterface $Credential = null,
        Guzzle $Guzzle = null,
        LoggerInterface $Logger = null
    ) {
        static::$instance = new Client($Credential, $Guzzle, $Logger);
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $uri, $options = [])
    {
        $response = $this->Guzzle->request($method, $uri, $options);
        if ('401' == $response->getStatusCode() && $response->hasHeader('Www-Authenticate')) {
            if (!is_a($this->Credential, BasicCredential::class)) {
                throw new \Exception('No BasicCredential provided!');
            }
            $this->Credential->requestTokenFromWwwAuthentication($response->getHeader('Www-Authenticate'));
            $response = $this->Guzzle->request($method, $uri, $options);
        }

        return $response;
    }
}