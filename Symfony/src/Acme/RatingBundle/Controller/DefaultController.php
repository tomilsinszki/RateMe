<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->add('alphanumeric_value', 'text')
            ->getForm();

        return $this->render('AcmeRatingBundle:Default:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
