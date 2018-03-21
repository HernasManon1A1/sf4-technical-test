<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(AuthenticationUtils $authenticationUtils, Request $request)
    {
        $user = new User();
        $form = $this->createForm(LoginType::class, $user)->remove('email');
        $error = $authenticationUtils->getLastAuthenticationError();
        $form->handleRequest($request);
        if (
            $form->isSubmitted() &&
            $form->isValid()
        ) {
            return $this->redirectToRoute('home');
        }

        return $this->render('login/login.html.twig', [
            'form' => $form->createView(),
            'error'         => $error
        ]);
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(LoginType::class, $user);

        $form->handleRequest($request);
        if (
            $form->isSubmitted() &&
            $form->isValid()
        ) {
            $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            try {
                $em->flush();
                $this->addFlash('success', 'Nouveau compte créé avec succès');
                return $this->redirectToRoute('login');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('login/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
    }
}
