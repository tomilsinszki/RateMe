<?php

namespace Acme\RatingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EditRateableForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('typeName')
            ->add('identifier', 'text', array(
                'mapped' => false,
                'required' => false,
                'error_bubbling' => true,
            ))
        ;
    }

    public function getName() {
        return 'edit_rateable_form';
    }

} 