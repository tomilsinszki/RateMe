<?php

namespace Acme\RatingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NewRateableForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rateableName')
            ->add('rateableTypeName')
            ->add('username')
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'A két jelszó nem egyezik!',
                'error_bubbling' => true,
            ))
            ->add('viaPhone', 'checkbox', array(
                'label' => 'Telefonos ügyfélszolgálatos',
                'required' => false,
            ))
            ->add('identifier')
        ;
    }

    public function getName() {
        return 'new_rateable_form';
    }

} 
