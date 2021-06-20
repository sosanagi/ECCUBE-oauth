<?php


namespace Customize\Security\OAuth2\Client\Provider\Yahoo;


use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class YConnect extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @inheritDoc
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://auth.login.yahoo.co.jp/yconnect/v2/authorization';
    }

    /**
     * @inheritDoc
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://auth.login.yahoo.co.jp/yconnect/v2/token';
    }

    /**
     * @inheritDoc
     */
    protected function getAccessTokenOptions(array $params)
    {
        $options = parent::getAccessTokenOptions([
            'grant_type' => 'authorization_code',
            'code' => $params['code'],
            'redirect_uri' => $params['redirect_uri']
        ]);

        $options['headers']['Authorization'] = 'Basic '.base64_encode($params['client_id'].':'.$params['client_secret']);
        return $options;
    }

    /**
     * @inheritDoc
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://userinfo.yahooapis.jp/yconnect/v2/attribute';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultScopes()
    {
        return [
            'openid'
        ];
    }

    /**
     * @inheritDoc
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if(isset($data['error'])) {
            $message = $data['error'];

            if(isset($data['error_description'])) {
                $message .= ":".$data['error_description'];
            }

            throw new IdentityProviderException($message, $response->getStatusCode(), $data);
        }
    }

    /**
     * @inheritDoc
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new YConnectResourceOwner($response);
    }
}