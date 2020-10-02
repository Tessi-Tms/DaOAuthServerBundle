<?php

namespace Da\OAuthServerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Da\OAuthServerBundle\Entity\User;


class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('plainPassword', PasswordType::class, array('required' => true))
            ->add('authSpace')
            ->add('raw')
            ->add('roles')
            ->add('enabled', null, array('required' => false))
            ->add('locked', null, array('required' => false))
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $user = $event->getData();
                if ($user && null !== $user->getId()) {
                    $form->add('plainPassword', PasswordType::class, array('required' => false));
                }
            }
        );
    }

    /**
    * {@inheritdoc}
    */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Da\OAuthServerBundle\Entity\User'
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'da_oauthserverbundle_usertype';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
