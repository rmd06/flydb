<?php

namespace Fly\FlydbBundle\Form;

use Symfony\Component\Form\Type;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('maxPerPage', null, array(
                        'label' => 'Flylines per page:',
                        'data' => 15
                        ));
    }

    public function getName()
    {
        return 'fly_user_registration';
    }
}
