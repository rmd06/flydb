<?php

namespace Fly\FlydbBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FlylineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('genotype')
            ->add('origin')
            ->add('tag')
            ->add('note')
            //->add('created', 'hidden')
            //->add('updated', 'hidden')
            //->add('cared', 'hidden')
            //->add('owner')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fly\FlydbBundle\Entity\Flyline'
        ));
    }

    public function getName()
    {
        return 'fly_flydbbundle_flylinetype';
    }
}
