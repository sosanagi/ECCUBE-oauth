<?php


namespace Customize\Controller;


use Eccube\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * @Route("/line")
 *
 * Class LineController
 * @package Customize\Controller
 */
class LineController extends AbstractController
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        TokenStorageInterface $tokenStorage
    ) {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/", name="line")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index()
    {
        return $this->get('oauth2.registry')
            ->getClient('line_client')
            ->redirect([
                "scope" => "openid profile email"
            ]);
    }

    /**
     * @Route("/callback", name="line_callback")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function callback()
    {
        if($this->isGranted("IS_AUTHENTICATED_FULLY")) {
            return $this->redirectToRoute("mypage");
        }else{
            return $this->redirectToRoute("line");
        }
    }
}