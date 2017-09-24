<?php
/**
 * Created by PhpStorm.
 * User: Marko Kunic
 * Date: 9/1/17
 * Time: 8:13 PM
 */

namespace AppBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Form\Type\SecurityRolesType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Admin\AbstractAdmin;

class UserAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('email');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('email')
            ->add('lastLogin')
            ->add('isSuspended')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('email')
                ->add('plainPassword', TextType::class, array(
                    'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                ))
                ->add('isSuspended', null, array('required' => false, 'label' => 'Suspended'));

        $authorizationChecker = $this->getConfigurationPool()
            ->getContainer()
            ->get('security.authorization_checker');

        if ($authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper
                    ->add('roles', SecurityRolesType::class, [
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false,
                    ]);
        }
    }
}
