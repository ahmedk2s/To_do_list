<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;


class UserController extends AbstractController
{
    /**
 * This controller allow us to edit user's profile
 *
 * @param int $id
 * @param Request $request
 * @param EntityManagerInterface $manager
 * @param UserPasswordHasherInterface $hasher
 * @return Response
 */

#[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
public function edit(
    int $id,
    Request $request,
    EntityManagerInterface $manager,
    UserPasswordHasherInterface $hasher
): Response {
    $choosenUser = $manager->getRepository(User::class)->find($id);

    if (!$choosenUser) {
        throw $this->createNotFoundException('Utilisateur non trouvé.');
    }

    $form = $this->createForm(UserType::class, $choosenUser);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        if ($hasher->isPasswordValid($choosenUser, $form->getData()->getPlainPassword())) {
            $user = $form->getData();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les informations de votre compte ont bien été modifiées.'
            );

            return $this->redirectToRoute('app_taches_index');
        } else {
            $this->addFlash(
                'warning',
                'Le mot de passe renseigné est incorrect.'
            );
        }
    }

    return $this->render('/user/user_edit.html.twig', [
        'form' => $form->createView(),
    ]);
    }

}