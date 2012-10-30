<?php

namespace Fly\FlydbBundle\Form;

use Symfony\Component\Form\Type;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('maxPerPage', null, array('label' => 'Flylines per page: '));
    }

    public function getName()
    {
        return 'fly_user_profile';
    }
}
