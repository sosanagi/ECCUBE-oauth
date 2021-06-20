<?php


namespace Customize\Security\OAuth2\Client\Provider\Line;


use Firebase\JWT\JWT;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;


class LineResourceOwner implements ResourceOwnerInterface
{

    /**
     * @var array
     */
    protected $response;

    /**
     * @var AccessToken
     */
    protected $token;

    public function __construct(array $response,AccessToken $token)
    {
        $this->response = $response;
        $this->token = $token;
    }

    public function __call($name, $arguments)
    {
        $get = substr($name,0, 3);
        if ($get !== 'get') {
            return null;
        }
        $parameter = function($name) {
            return ltrim(strtolower(preg_replace('/[A-Z]/', '_\0', str_replace("get", "", $name))), '_');
        };
        return $this->getResource($parameter($name));
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getResource("userId");
    }

    public function getEmail() {

        // $id_token = $this->token->getValues()["openid"];
        // $tokens = explode(".", $id_token);
        // log_error($this->token->getToken(), [null]);

        $id_token = $this->token->getValues()["id_token"];
        $tokens = explode(".", $id_token);

        $payload = JWT::urlsafeB64Decode($tokens[1]);
        return isset(json_decode($payload)->email) ? json_decode($payload)->email : null;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->response;
    }

    protected function getResource(string $name)
    {
        return isset($this->response[$name]) ? $this->response[$name] : null;
    }
}