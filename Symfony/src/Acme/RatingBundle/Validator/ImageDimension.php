<?php

namespace Acme\RatingBundle\Validator;
 
use Symfony\Component\Validator\Constraint;
 
/**
* @Annotation
*/
class ImageDimension extends Constraint {
    public $message = 'A kép mérete nem megfelelő! Csak %sx%s méretű kép tölthető fel.';
    public $width = 350;
    public $height = 350;
}
