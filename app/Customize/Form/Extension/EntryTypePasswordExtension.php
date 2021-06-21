<?php


namespace Customize\Form\Extension;

use Eccube\Entity\Customer;
use Eccube\Form\Type\Front\EntryType;
use Eccube\Util\StringUtil;
use KnpU\OAuth2ClientBundle\Security\Helper\FinishRegistrationBehavior;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class EntryTypePasswordExtension extends AbstractTypeExtension
{
    use FinishRegistrationBehavior;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    public function __construct(
        RequestStack $requestStack,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->requestStack = $requestStack;
        $this->encoderFactory = $encoderFactory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userInfo = $this->getUserInfoFromSession($this->requestStack->getCurrentRequest());

        if($userInfo) {
            $builder->remove('password');

            $builder
                ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event){
                    $Customer = $event->getData();
                    if($Customer instanceof Customer) {
                        // ランダムパスワードを生成
                        $password = StringUtil::random();
                        $encoder = $this->encoderFactory->getEncoder($Customer);
                        $Customer->setPassword($encoder->encodePassword($password, $Customer->getSalt()));
                    }
                });
        }
    }

    /**
     * @inheritDoc
     */
    public function getExtendedType()
    {
        return EntryType::class;
    }
}