<?php


namespace Customize\Controller;


use Eccube\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/yahoo")
 *
 * Class YahooController
 * @package Customize\Controller
 */
class YahooController extends AbstractController
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        TokenStorageInterface $tokenStorage
    )
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/", name="yahoo")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index()
    {
        return $this->get('oauth2.registry')
            ->getClient('yconnect_client')
            ->redirect([
                "scope" => "openid profile email address"
            ]);
    }

    /**
     * @Route("/callback", name="yahoo_callback")
     */
    public function callback()
    {
        if($this->isGranted("IS_AUTHENTICATED_FULLY")) {
            return $this->redirectToRoute("mypage");
        }else{
            return $this->redirectToRoute("yahoo");
        }
    }
}