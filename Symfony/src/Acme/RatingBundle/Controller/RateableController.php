<?php

namespace Acme\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class RateableController extends Controller
{
    public function indexAction($alphanumericValue)
    {
        return new Response("<html><body>Rateable ($alphanumericValue)</body></html>");
    }
}
