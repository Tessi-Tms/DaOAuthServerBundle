<?php

namespace Da\OAuthServerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as SfType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Da\OAuthServerBundle\Form\Type\SerializedArrayType;

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
            ->add('redirectUris', SerializedArrayType::class, array(
                'entry_type' => SfType\UrlType::class,
            ))
            ->add('allowedGrantTypes', SerializedArrayType::class, array(
                'entry_type' => SfType\TextType::class,
            ))
            ->add('trusted', SfType\CheckboxType::class, array('required' => false))
            ->add('clientLoginPath')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Da\OAuthServerBundle\Entity\Client'
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
        return 'da_oauthserverbundle_clienttype';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
