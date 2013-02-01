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
        $ratings = $this->getDoctrine()->getRepository('AcmeRatingBundle:Rating')->findBy(array('ratingUser' => $user), array('created' => 'DESC'));
        
        return $this->render('AcmeUserBundle:Default:profile.html.twig', array(
            'user' => $user,
            'ratingCount' => count($ratings),
            'ratingAverage' => $this->getRatingsAverageWithTwoDecimals($ratings),
            'ratings' => $ratings,
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

    public function profileEditAction(Request $request)
    {
        $user = $this->getUserFromContext();
        $emailForm = $this->createFormBuilder($user)->add('username', 'text', array('label'  => 'e-mail'))->getForm();
        return $this->renderProfileEditView($emailForm);
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

                return $this->redirect($this->generateUrl('acme_user_profile_edit'));
            }
        }
    }

    public function updateUserDataAction()
    {
        $tmpUser = new User();
        $emailForm = $this->createFormBuilder($tmpUser)->add('username', 'text', array('label'  => 'e-mail'))->getForm();

        if ( $this->getRequest()->isMethod('POST') ) {
            $emailForm->bind($this->getRequest());

            if ( $emailForm->isValid() ) {
                $entityManager = $this->getDoctrine()->getManager();
                $user = $this->getUserFromContext();
                $tmpUser = $emailForm->getData();
                $user->setUsername($tmpUser->getUsername());
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('acme_user_profile_edit'));
            }
        }
        
        return $this->renderProfileEditView($emailForm);
    }

    private function renderProfileEditView($emailForm) {
        $user = $this->getUserFromContext();
        $imageURL = $this->getImageURLForUser($user);

        $image = new Image();
        $imageUploadForm = $this->createFormBuilder($image)->add('file')->getForm();

        return $this->render('AcmeUserBundle:Default:profileEdit.html.twig', array(
            'user' => $user,
            'imageUploadForm' => $imageUploadForm->createView(),
            'emailForm' => $emailForm->createView(),
            'imageURL' => $imageURL,
        ));
    }

    private function getUserFromContext()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if ( empty($user) === TRUE )
            throw $this->createNotFoundException('Current user could not be found.');

        return $user;
    }

    private function getRatingsAverageWithTwoDecimals($ratings)
    {
        $ratingSum = 0.0;

        foreach($ratings AS $rating) 
            $ratingSum += $rating->getStars();
        
        if ( count($ratings) == 0 )
            return 0.0;

        $average = (float)$ratingSum / (float)count($ratings);
        $average = round($average, 2);
        
        return number_format($average, 2, ',', ' ');
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
        return $this->render('AcmeUserBundle:Default:registrationDone.html.twig', array());
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
                $raterGroup = $this->getDoctrine()->getRepository('AcmeUserBundle:Group')->findOneByName('rater');
                
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($password);
                $user->setEmail($user->getUsername());
                $user->addGroup($raterGroup);
                
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                
                return $this->redirect($this->generateUrl('acme_user_registration_done'));
            }
        }
    }

    public function changePasswordAction()
    {
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
                    ->add('oldPassword', 'password', array('label' => 'régi jelszó:'))
                    ->add('newPassword1', 'password', array('label' => 'új jelszó:'))
                    ->add('newPassword2', 'password', array('label' => 'új jelszó megerősítése:'))
                    ->getForm();

        return $this->render('AcmeUserBundle:Default:changePassword.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function updatePasswordAction()
    {
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
                    ->add('oldPassword', 'password', array('label' => 'régi jelszó:'))
                    ->add('newPassword1', 'password', array('label' => 'új jelszó:'))
                    ->add('newPassword2', 'password', array('label' => 'új jelszó megerősítése:'))
                    ->getForm();
       
        if ( $this->getRequest()->isMethod('POST') === TRUE ) {
            $form->bind($this->getRequest());
            $user = $this->getUserFromContext();
            $data = $form->getData();

            $isOldPasswordValid = ( $this->isPasswordValidForCurrentUser($data['oldPassword']) === TRUE );
            $doNewPasswordsMatch = ( $data['newPassword1'] === $data['newPassword1'] );
            $isNewPasswordNonEmpty = ( empty($data['newPassword1']) === FALSE );
            $isNewPasswordString = ( is_string($data['newPassword1']) === TRUE );
            $isNewPasswordLongEnough = ( 4 < strlen($data['newPassword1']) );

            if ( $isOldPasswordValid AND $doNewPasswordsMatch AND $isNewPasswordNonEmpty AND $isNewPasswordString AND $isNewPasswordLongEnough ) {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($data['newPassword1'], $user->getSalt());
                $user->setPassword($password);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            }
        }

        return $this->redirect($this->generateUrl('acme_user_profile'));
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
    
    public function isPasswordValidAction()
    {
        $isPasswordValid = FALSE;
        $request = $this->getRequest();

        if ( $request->isXmlHttpRequest() !== TRUE ) {
            return new Response(json_encode($isPasswordValid), 200, array('Content-Type' => 'application/json'));
        }
        
        if ( $this->isPasswordValidForCurrentUser($request->request->get('password')) === TRUE ) {
            $isPasswordValid = TRUE;
        }
        
        return new Response(json_encode($isPasswordValid), 200, array('Content-Type' => 'application/json'));
    }

    private function isPasswordValidForCurrentUser($possiblePassword)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $possiblePasswordEncoded = $encoder->encodePassword($possiblePassword, $user->getSalt());

        if ( $possiblePasswordEncoded === $user->getPassword() ) {
            return TRUE;
        }

        return FALSE;
    }
}
