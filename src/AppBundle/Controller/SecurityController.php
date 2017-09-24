<?php
/**
 * Created by PhpStorm.
 * User: Marko Kunic
 * Date: 9/23/17
 * Time: 6:46 PM
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\LoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends Controller
{
    /**
     * @Route("/admin/login", name="admin_login")
     */
    public function loginAction()
    {
        $helper = $this->get('security.authentication_utils');

        $form = $this->createForm(LoginType::class, [
            'email' => $helper->getLastUsername()
        ]);

        return $this->render('AppBundle:Security:login.html.twig', [
            'last_username' => $helper->getLastUsername(),
            'form' => $form->createView(),
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/admin/logout", name="admin_logout")
     */
    public function logoutAction()
    {
    }
}
