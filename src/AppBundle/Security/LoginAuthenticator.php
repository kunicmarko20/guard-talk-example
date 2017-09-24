<?php
/**
 * Created by PhpStorm.
 * User: Marko Kunic
 * Date: 9/13/17
 * Time: 12:21
 */

namespace AppBundle\Security;

use AppBundle\Form\Type\LoginType;
use AppBundle\Entity\User;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginAuthenticator extends AbstractFormLoginAuthenticator
{
    private $formFactory;
    private $passwordEncoder;
    private $router;

    public function __construct(
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getCredentials(Request $request)
    {
        if ($request->getPathInfo() != '/admin/login' || $request->getMethod() != 'POST') {
            return null;
        }

        $form = $this->formFactory->create(LoginType::class);
        $form->handleRequest($request);

        $data = $form->getData();

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['email']
        );

        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['email']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!$this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            return false;
        }

        /** @var User $user */
        if ($user->isSuspended()) {
            throw new CustomUserMessageAuthenticationException('Suspended');
        }

        if (!$user->hasRole(User::ADMIN_ROLE)) {
            throw new CustomUserMessageAuthenticationException("You don't have permission to access that page.");
        }

        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        $url = $this->router->generate('admin_login');

        return new RedirectResponse($url);
    }

    public function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('sonata_admin_dashboard');
    }

    public function supportsRememberMe()
    {
    }

    protected function getLoginUrl()
    {
        $url = $this->router->generate('admin_login');

        return new RedirectResponse($url);
    }
}
