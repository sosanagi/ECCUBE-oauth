<?php


namespace Customize\Controller;


use Eccube\Controller\AbstractController;
// use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * @Route("/firebase")
 *
 * Class FirebaseController
 * @package Customize\Controller
 */
class FirebaseController extends AbstractController
{
    /**
     * 
     * @Route("/", name="firebase")
     * @Template("Firebase/index.twig")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index()
    {
        return [];
    }

    /**
     * @Route("/callback", name="firebase_callback")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function callback()
    {
        if($this->isGranted("IS_AUTHENTICATED_FULLY")) {
            return $this->redirectToRoute("mypage");
        }else{
            return $this->redirectToRoute("firebase");
        }
    }
}