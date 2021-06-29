<?php

namespace Customize\Controller\Admin;

use Customize\Form\Type\Admin\FirebaseType;
use Customize\Repository\CustomerRepository;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Customer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Kreait\Firebase\Auth;

class FirebaseConfigController extends AbstractController
{

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    public function __construct(
        Auth $auth,
        CustomerRepository $customerRepository
    ) {
        $this->auth = $auth;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/config", name="firebase_admin_config")
     * @Template("@admin/config.twig")
     */
    public function index(Request $request)
    {
        $form = $this->createForm(FirebaseType::class);
        $form->handleRequest($request);

        if ($form->get('firebase_delete')->isSubmitted() ) {
            $firebase_users = $this->auth->listUsers($defaultMaxResults = 1000, $defaultBatchSize = 1000);
            $Coustmers = $this->customerRepository->loadUser();
            $firebase_ids = [];

            foreach ($Coustmers as $user) {
                array_push($firebase_ids, $user["firebase_uid"]);
            }

            foreach ($firebase_users as $user) {
                /** @var \Kreait\Firebase\Auth\UserRecord $user */
                if (!in_array($user->uid, $firebase_ids)) {
                    $this->auth->deleteUser($user->uid);
                }
            }

            $this->addSuccess('削除しました。', 'admin');

            return $this->redirectToRoute('firebase_admin_config');
        }

        return [
            'form' => $form->createView()
        ];
    }
}
