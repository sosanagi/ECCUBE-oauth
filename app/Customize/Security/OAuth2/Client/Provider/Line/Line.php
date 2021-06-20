<?php


namespace Customize\Security\OAuth2\Client\Provider\Line;


use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Line extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @inheritDoc
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://access.line.me/oauth2/v2.1/authorize';
    }

    /**
     * @inheritDoc
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://api.line.me/oauth2/v2.1/token';
    }

    /**
     * @inheritDoc
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://api.line.me/v2/profile';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultScopes()
    {
        return [
            'openid'
            // ,"email"
        ];
    }

    /**
     * @inheritDoc
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if(!empty($data['error'])) {
            $code = 0;
            $error = $data['error'];

            if(is_array($error)) {
                $code = $error['code'];
                $message = $error['message'];
            }

            throw new IdentityProviderException($message, $response->getStatusCode(), $data);
        }
    }

    /**
     * @inheritDoc
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new LineResourceOwner($response, $token);
    }
}