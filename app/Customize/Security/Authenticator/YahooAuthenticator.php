<?php


namespace Customize\Security\Authenticator;


use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Customer;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Security\Exception\FinishRegistrationException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class YahooAuthenticator extends SocialAuthenticator
{
    const OAUTH2_PROVIDER = "yahoo";

    /**
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'yahoo_callback';
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            $this->router->generate("yahoo"),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        try {
            return $this->fetchAccessToken($this->getYahooClient());
        } catch (AuthenticationException $e) {
            throw new $e;
        }

    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $userInfo = $this->getYahooClient()
            ->fetchUserFromToken($credentials);

        // Yahooでメールアドレス認証していない場合がある
        if (!$userInfo->getEmailVerified()) {
            throw new IdentityProviderException('Yahooでメールアドレスが認証されていません。');
        }

        // ヤフー連携済みの場合
        $Customer = $this->entityManager->getRepository(Customer::class)
            ->findOneBy(['yahoo_user_id' => $userInfo->getId()]);
        if ($Customer) {
            return $Customer;
        }

        $Customer = $this->entityManager->getRepository(Customer::class)
            ->findOneBy(['email' => $userInfo->getEmail()]);

        // 会員登録していない場合、会員登録ページへ
        if (!$Customer) {
            throw new FinishRegistrationException(array_merge($userInfo->toArray(), ["provider" => self::OAUTH2_PROVIDER]));
        }

        // 通常の会員登録済みの場合はユーザー識別子を保存
        $Customer->setYahooUserId($userInfo->getId());
        $this->entityManager->persist($Customer);
        $this->entityManager->flush();

        return $Customer;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // 会員登録していない場合
        if ($exception instanceof FinishRegistrationException) {
            $this->saveUserInfoToSession($request, $exception);
            return new RedirectResponse($this->router->generate("entry"));
        } else {
            $this->saveAuthenticationErrorToSession($request, $exception);
            return new RedirectResponse($this->router->generate("mypage_login"));
        }
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetUrl = $this->router->generate("mypage");

        return new RedirectResponse($targetUrl);

    }

    /**
     * EC-CUBEがUsernamePasswordTokenなので合わせる
     *
     * @param UserInterface $user
     * @param string $providerKey
     * @return UsernamePasswordToken|\Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken
     */
    public function createAuthenticatedToken(UserInterface $user, $providerKey)
    {
        if ($user instanceof Customer && $providerKey === 'customer') {
            return new UsernamePasswordToken($user, null, $providerKey, ['ROLE_USER']);
        }

        return parent::createAuthenticatedToken($user, $providerKey);
    }

    private function getYahooClient()
    {
        return $this->clientRegistry
            ->getClient('yconnect_client');
    }
}