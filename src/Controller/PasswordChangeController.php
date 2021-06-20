<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordChangeFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordChangeController extends AbstractController
{
    #[Route('/password/change', name: 'password_change')]
    public function passChange(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PasswordChangeFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $pastPassword=$form->get('pastPassword')->getData();
            $newPassword=$form->get('newPassword')->getData();
            $check = $user->getPassword();
            $checkpast = $passwordEncoder->encodePassword(
                $user,
                $pastPassword
            );
            if ($pastPassword != $newPassword
                && $checkpast != $check ) {

                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $newPassword
                        )
                    );
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();

                    return $this->redirectToRoute('app_login');

            }
            return new Response('You are not user!', Response::HTTP_FORBIDDEN);
        }

        return $this->render('password_change/index.html.twig', [
            'PasswordChangeForm' => $form->createView(),
        ]);
    }




}
