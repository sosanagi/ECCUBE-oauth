<?php

namespace Customize\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use KnpU\OAuth2ClientBundle\Security\Helper\FinishRegistrationBehavior;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class EntryEventSubscriber implements EventSubscriberInterface
{
    use FinishRegistrationBehavior;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        SessionInterface $session,
        EntityManagerInterface $entityManager
    )
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            EccubeEvents::FRONT_ENTRY_INDEX_COMPLETE => 'onFrontEntryIndexComplete'
        ];
    }

    public function onFrontEntryIndexComplete(EventArgs $args)
    {
        $request = $args->getRequest();

        if(null === $request) {
            return;
        }

        $userInfo = $this->getUserInfoFromSession($request);
        if($userInfo) {

            // 会員登録完了時にAuthのセッション削除
            $this->session->remove('guard.finish_registration.user_information');
        }
        
    }
}
