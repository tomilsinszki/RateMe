<?php

namespace Acme\QuizBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller {

    /**
     * @Route("/quiz/{rateableId}")
     * @Template()
     */
    public function indexAction($rateableId) {
        $a = new Answer();
        return array('name' => $rateableId);
    }
}
