<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PropertyType extends AbstractType
{

    protected $choices;

    public function __construct($choices = NULL) {
        if(is_null($choices) == false){
            $this->choices = $choices;
        }
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')    
            ->add('address')    
            ->add('images', 'collection', array(
                'type'          => new ImageType(),
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false,
                'label'         => false
            )
        );

        if($options['property_site_visible'] == true) {
            if($this->choices == NULL) {
                $builder->add('site', 'entity', array(
                    'class'         => 'AppBundle\Entity\Site',
                    'property'      => 'name',
                    'choices'       => $this->choices 
                ));  
            } else {
                $builder->add('site', 'entity', array(
                    'class'         => 'AppBundle\Entity\Site',
                    'property'      => 'name'
                ));  
            }
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Property',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention' => 'property_item_intention',
            'property_site_visible' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_property';
    }
}
