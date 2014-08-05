<?php

namespace Acme\RatingBundle\Validator;
 
use Symfony\Component\HttpFoundation\File\UploadedFile;
 
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
 
class ImageDimensionValidator extends ConstraintValidator {
    public function isValid($value, Constraint $constraint) {
        $isValid = false;
        
        if ($value instanceof UploadedFile) {
            $imageSize = getimagesize($value);
             
            if ($imageSize !== false && is_array($imageSize)) {
                if ($imageSize[0]==$constraint->width and $imageSize[1]==$constraint->height) {
                    $isValid = true;
                }
            }
        }
         
        if (!$isValid) {
            $this->setMessage(sprintf($constraint->message, $constraint->width, $constraint->height));
        }
         
        return $isValid;
    }
}
