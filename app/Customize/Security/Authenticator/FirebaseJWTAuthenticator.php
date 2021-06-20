<?php

namespace Customize\Security\Authenticator;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Customer;
// use Kreait\Firebase;
// use Kreait\Firebase\Factory;
// use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Exception\FinishRegistrationException;
use KnpU\OAuth2ClientBundle\Security\Helper\FinishRegistrationBehavior;

class FirebaseJWTAuthenticator extends AbstractGuardAuthenticator
{
    use FinishRegistrationBehavior;

    const OAUTH2_PROVIDER = "firebase";
    // /**
    //  * @var Firebase
    //  */
    // private $firebase;

    // public function __construct(string $serviceAccountKeyJson)
    // {
    //     // @see https://firebase-php.readthedocs.io/en/latest/setup.html
    //     $this->firebase = (new Factory())
    //         ->withServiceAccount(ServiceAccount::fromJson($serviceAccountKeyJson))
    //         ->create();
    // }
    

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        Auth $auth,
        EntityManagerInterface $entityManager,
        RouterInterface $router
    ) {
        //@see https://firebase-php.readthedocs.io/en/stable/authentication.html
        $this->auth = $auth;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            $this->router->generate("firebase"),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        // return $request->headers->has('Authorization');
        return $request->attributes->get('_route') === 'firebase_callback';
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        // preg_match('/Bearer +(.+)$/', $request->headers->get('Authorization'), $m);
        $idToken = $request->query->get('id_token');

        if (empty($idToken)) {
            // throw new BadRequestHttpException('idToken was not found');
            return null;
        }

        return $idToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            // $verifiedIdToken = $this->firebase->getAuth()->verifyIdToken($credentials['id_token']);
            $verifiedIdToken = $this->auth->verifyIdToken($credentials);
        } catch (InvalidArgumentException $e) {
            throw new AuthenticationException($e->getMessage());
        }

        // @see https://firebase.google.com/docs/auth/admin/verify-id-tokens?hl=ja
        // $uid = $verifiedIdToken->getClaim('sub');
        // https://github.com/kreait/firebase-php/discussions/531
        $uid = $verifiedIdToken->claims()->get('sub');
        $email = $verifiedIdToken->claims()->get('email');


        // firebase連携済みの場合
        $Customer = $this->entityManager->getRepository(Customer::class)
            ->findOneBy(['firebase_uid' => $uid]);
        if($Customer) {
            return $Customer;
        }

        // firebase連携なしかつemail一致ありの場合
        $Customer = $this->entityManager->getRepository(Customer::class)
            ->findOneBy(['email' => $email]);

        // 会員登録していない場合、会員登録ページへ
        if(!$Customer) {
            throw new FinishRegistrationException(["userId" => $uid, "email" => $email, "provider" => self::OAUTH2_PROVIDER]);
        }
        // 通常の会員登録済みの場合はユーザー識別子を保存
        $Customer->setFirebaseUserId($uid);
        $this->entityManager->persist($Customer);
        $this->entityManager->flush();

        // return $userProvider->loadUserByUsername($uid);
        return $Customer;
        // return new RedirectResponse($this->router->generate("mypage_login"));
        // return $userProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        
        // return new JsonResponse(['message' => strtr($exception->getMessageKey(), $exception->getMessageData())], Response::HTTP_FORBIDDEN);

        // 会員登録していない場合
        if ($exception instanceof FinishRegistrationException) {
            $this->saveUserInfoToSession($request, $exception);
            return new RedirectResponse($this->router->generate("entry"));
        } else {
            // $this->saveAuthenticationErrorToSession($request, $exception);
            return new RedirectResponse($this->router->generate("mypage_login"));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetUrl = $this->router->generate("mypage");

        return new RedirectResponse($targetUrl);
    }
    

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }

    // /**
    //  * EC-CUBEがUsernamePasswordTokenなので合わせる
    //  *
    //  * @param UserInterface $user
    //  * @param string $providerKey
    //  * @return UsernamePasswordToken|\Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken
    //  */
    // public function createAuthenticatedToken(UserInterface $user, $providerKey)
    // {
    //     if ($user instanceof Customer && $providerKey === 'customer') {
    //         return new UsernamePasswordToken($user, null, $providerKey, ['ROLE_USER']);
    //     }
 
    //     return parent::createAuthenticatedToken($user, $providerKey);
    // }


}