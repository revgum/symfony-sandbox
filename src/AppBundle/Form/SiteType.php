<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SiteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('properties', 'collection', array(
                'type'          => new PropertyType(),
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false,
                'label'         => false,
                'options'       => array(
                    'property_site_visible' => false
                )
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Site',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention' => 'site_item_intention'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_site';
    }

}
