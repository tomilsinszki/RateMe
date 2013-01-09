<?php

namespace Acme\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\UserBundle\Entity\User;
use Acme\RatingBundle\Entity\Rating;
use Acme\RatingBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    public function profileAction(Request $request)
    {
        $user = $this->getUserFromContext();
        $imageURL = $this->getImageURLForUser($user);
        $ratings = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rating')->findByRatingUser($user);
        
        $image = new Image();
        $imageUploadForm = $this->createFormBuilder($image)->add('file')->getForm();
        
        return $this->render('AcmeUserBundle:Default:profile.html.twig', array(
            'user' => $user,
            'ratingCount' => count($ratings),
            'ratingAverage' => $this->getRatingsAverage($ratings),
            'ratings' => $ratings,
            'imageUploadForm' => $imageUploadForm->createView(),
            'imageURL' => $imageURL,
        ));
    }

    private function getImageURLForUser($user)
    {
        $imageURL = null;
        $image = $user->getImage();
        if ( empty($image) === FALSE )
            $imageURL = $image->getWebPath();
        
        return $imageURL;
    }

    public function uploadImageAction()
    {
        $user = $this->getUserFromContext();
        $image = new Image();
        $imageUploadForm = $this->createFormBuilder($image)->add('file')->getForm();
        
        if ( $this->getRequest()->isMethod('POST') ) {
            $imageUploadForm->bind($this->getRequest());

            if ( $imageUploadForm->isValid() ) {
                $entityManager = $this->getDoctrine()->getManager();
                $user->setImage($image);
                $entityManager->persist($image);
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('acme_user_profile'));
            }
        }
    }

    private function getUserFromContext()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if ( empty($user) === TRUE )
            throw $this->createNotFoundException('Current user could not be found.');

        return $user;
    }

    private function getRatingsAverage($ratings)
    {
        $ratingSum = 0.0;

        foreach($ratings AS $rating) 
            $ratingSum += $rating->getStars();
        
        if ( count($ratings) == 0 )
            return 0.0;

        return (float)$ratingSum / (float)count($ratings);
    }

    public function registerAction(Request $request)
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('username', 'text')
            ->add('password', 'text')
            ->getForm();
        
        return $this->render('AcmeUserBundle:Default:registration.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    public function registrationDoneAction(Request $request)
    {
        return $this->render('AcmeUserBundle:Default:registration_done.html.twig', array());
    }

    public function newAction(Request $request)
    {
        $user = new User();
        
        $form = $this->createFormBuilder($user)
            ->add('username', 'text')
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

                $user->setEmail($user->getUsername());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('acme_user_registration'));
            }
        }
    }

    public function doesExistAction()
    {
        $request = $this->getRequest();
        $username = $request->get('username');

        $user = $this->getDoctrine()->getRepository('AcmeUserBundle:User')->findOneByUsername($username);

        if ( empty($user) === FALSE )
            return new Response(json_encode(TRUE));
        else
            return new Response(json_encode(FALSE));
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
