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
            ->add('location')
            ->add('origin', null, array('data' => 'NA'))
            ->add('tag', null, array('data' => 'NA'))
            ->add('note', null, array('data' => 'NA'))
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
