<?php


namespace DockerRegistry\Credential;


class BasicCredential extends AbstractCredential
{
    static protected $token;

    protected $username;
    protected $password;

    /**
     * @param string $username
     * @param string $password
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function __construct($username = null, $password = null)
    {
        $this->username = $username;
        $this->password = $password;

        return false;
    }

    public function requestTokenFromWwwAuthentication($authentication)
    {
        [$uri, $options] = $this->parseWwwAuthentication($authentication);

        $Client = new \GuzzleHttp\Client();

        $response = $Client->get($uri, [
            'query'   => $options,
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode("{$this->username}:{$this->password}")
            ]
        ]);

        $json = json_decode((string)$response->getBody(), true);

        static::$token = (is_array($json) && array_key_exists('token', $json)) ? $json['token'] : false;

        return static::$token;
    }

    private function parseWwwAuthentication($authentication)
    {
        if (is_array($authentication)) {
            $authentication = $authentication[0];
        }

        $authentication      = substr($authentication, strlen('Bearer '));
        $authenticationParts = explode(',', $authentication);

        $uri     = '';
        $options = [];

        foreach ($authenticationParts as $authenticationPart) {
            $parts    = explode('=', $authenticationPart);
            $parts[1] = trim($parts[1], '"');
            switch ($parts[0]) {
                case 'realm':
                    $uri = $parts[1];
                    break;
                default:
                    $options[$parts[0]] = $parts[1];
            }
        }

        return [$uri, $options];
    }

    public function getToken()
    {
        return static::$token;
    }
}