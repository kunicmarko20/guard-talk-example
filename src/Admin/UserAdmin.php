<?php

namespace App\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
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
            ->add('plainPassword', TextType::class, [
                'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
            ])
            ->add('isSuspended', null, ['required' => false, 'label' => 'Suspended']);
    }
}
