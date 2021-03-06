<?php

namespace Da\OAuthServerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClientType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('scope')
            ->add('authSpace')
            ->add('redirectUris', 'serialized_array', array(
                'type' => 'url',
            ))
            ->add('allowedGrantTypes', 'serialized_array', array(
                'type' => 'text',
            ))
            ->add('trusted', 'switch_checkbox', array('required' => false))
            ->add('clientLoginPath')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Da\OAuthServerBundle\Entity\Client'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'da_oauthserverbundle_clienttype';
    }
}
