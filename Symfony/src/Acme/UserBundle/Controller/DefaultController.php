<?php

namespace Acme\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\UserBundle\Entity\User;
use Acme\RatingBundle\Entity\Rating;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    public function profileAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if ( empty($user) === TRUE )
            throw $this->createNotFoundException('Current user could not be found.');
        
        $ratings = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rating')->findByRatingUser($user);
        
        return $this->render('AcmeUserBundle:Default:profile.html.twig', array(
            'user' => $user,
            'ratingCount' => count($ratings),
            'ratingAverage' => $this->getRatingsAverage($ratings),
            'ratings' => $ratings
        ));
    }

    private function getRatingsAverage($ratings)
    {
        $ratingSum = 0.0;

        foreach($ratings AS $rating) 
            $ratingSum += $rating->getStars();

        return (float)$ratingSum / (float)count($ratings);
    }

    public function registerAction(Request $request)
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('username', 'text')
            ->add('email', 'text')
            ->add('password', 'text')
            ->getForm();
        
        return $this->render('AcmeUserBundle:Default:registration.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function newAction(Request $request)
    {
        $user = new User();
        
        $form = $this->createFormBuilder($user)
            ->add('username', 'text')
            ->add('email', 'text')
            ->add('password', 'text')
            ->getForm();

        if ( $request->isMethod('POST') ) {
            $form->bind($request);

            if ( $form->isValid() ) {
                $factory = $this->get('security.encoder_factory');
                
                $user = $form->getData();

                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($password);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('acme_user_registration'));
            }
        }
    }

    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('AcmeUserBundle:Default:login.html.twig', array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }
}
