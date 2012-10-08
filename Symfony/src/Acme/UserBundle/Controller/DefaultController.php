<?php

namespace Acme\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AcmeUserBundle:Default:index.html.twig', array('name' => $name));
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
}
