<?php


namespace Customize\Security\OAuth2\Client\Provider\Yahoo;


use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class YConnectResourceOwner implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $response;

    public function __construct(array $response = [])
    {
        $this->response = $response;
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
        return $this->getResource("sub");
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