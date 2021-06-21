<?php

namespace Customize\Form\Extension;

// use Customize\Security\OAuth2\Client\Provider\Line\Line;
use Customize\Security\Authenticator\FirebaseJWTAuthenticator;
// use Customize\Security\Authenticator\LineAuthenticator;
// use Customize\Security\Authenticator\YahooAuthenticator;
use Eccube\Entity\Customer;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\Front\EntryType;
use Eccube\Form\Type\NameType;
use Eccube\Form\Type\PostalType;
use Eccube\Form\Type\RepeatedEmailType;
use Eccube\Repository\Master\PrefRepository;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use KnpU\OAuth2ClientBundle\Security\Helper\FinishRegistrationBehavior;

class EntryTypeExtension extends AbstractTypeExtension
{
    use FinishRegistrationBehavior;

    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var PrefRepository
     */
    private $prefRepository;

    public function __construct(
        RequestStack $requestStack,
        PrefRepository $prefRepository
    ) {
        $this->requestStack = $requestStack;
        $this->prefRepository = $prefRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userInfo = $this->getUserInfoFromSession($this->requestStack->getCurrentRequest());

        if($userInfo && $userInfo["provider"] === FirebaseJWTAuthenticator::OAUTH2_PROVIDER) {
            // メールアドレスをセット
            $builder
                ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($userInfo) {
                    $form = $event->getForm();

                    $form['email']->setData($userInfo["email"]);
                });

            // Firebaseユーザー識別子をCustomerにセット
            $builder
                ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($userInfo) {
                    $Customer = $event->getData();
                    if($Customer instanceof Customer) {
                        $Customer->setFirebaseUserId($userInfo["userId"]);
                    }
                });
        }

        // if($userInfo && $userInfo["provider"] === LineAuthenticator::OAUTH2_PROVIDER) {
        //     // メールアドレスをセット
        //     $builder
        //         ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($userInfo) {
        //             $form = $event->getForm();

        //             $form['email']->setData($userInfo["email"]);
        //         });

        //     // lineユーザー識別子をCustomerにセット
        //     $builder
        //         ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($userInfo) {
        //             $Customer = $event->getData();
        //             if($Customer instanceof Customer) {
        //                 $Customer->setLineUserId($userInfo["userId"]);
        //             }
        //         });
        // }

        // if($userInfo && $userInfo["provider"] === YahooAuthenticator::OAUTH2_PROVIDER) {
        //     // メールアドレスをセット
        //     $builder ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($userInfo) {
        //             $form = $event->getForm(); 
        //             $form['email']->setData($userInfo["email"]); 
        //         });

        //     // ユーザー識別子をCustomerにセット
        //     $builder
        //         ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($userInfo) {
        //             $Customer = $event->getData();
        //             if($Customer instanceof Customer) {
        //                 $Customer->setYahooUserId($userInfo["sub"]);
        //             }
        //         });
        // }
    }

    /**
    * {@inheritdoc}
    */
    public function getExtendedType()
    {
        return EntryType::class;
    }
}