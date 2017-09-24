<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Form\Type;

use AppBundle\Form\Transformer\RestoreRolesTransformer;
use AppBundle\Security\EditableRolesBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecurityRolesType extends AbstractType
{
    /**
     * @var EditableRolesBuilder
     */
    protected $rolesBuilder;

    /**
     * @param EditableRolesBuilder $rolesBuilder
     */
    public function __construct(EditableRolesBuilder $rolesBuilder)
    {
        $this->rolesBuilder = $rolesBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $transformer = new RestoreRolesTransformer($this->rolesBuilder);

        // GET METHOD
        $formBuilder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($transformer) {
            $transformer->setOriginalRoles($event->getData());
        });

        // POST METHOD
        $formBuilder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($transformer) {
            $transformer->setOriginalRoles($event->getForm()->getData());
        });

        $formBuilder->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attr = $view->vars['attr'];

        if (isset($attr['class']) && empty($attr['class'])) {
            $attr['class'] = 'sonata-medium';
        }

        $view->vars['attr'] = $attr;
        $view->vars['read_only_choices'] = $options['read_only_choices'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        list($roles, $rolesReadOnly) = $this->rolesBuilder->getRoles();

        $resolver->setDefaults(array(
            'choices' => function (Options $options, $parentChoices) use ($roles) {
                return empty($parentChoices) ? $roles : array();
            },

            'read_only_choices' => function (Options $options) use ($rolesReadOnly) {
                return empty($options['choices']) ? $rolesReadOnly : array();
            },

            'data_class' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_security_roles';
    }
}
