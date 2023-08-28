<?php

namespace App\Controller;

use App\Entity\Taches;
use App\Form\TachesType;
use App\Repository\TachesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/taches')]
class TachesController extends AbstractController
{
    #[Route('/', name: 'app_taches_index', methods: ['GET'])]
    public function index(TachesRepository $tachesRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();

        $statutFilter = $request->query->get('statut');

        $criteria = ['user' => $user];
        if ($statutFilter) {
            $criteria['statut'] = $statutFilter;
        }

        $taches = $paginator->paginate(
            $tachesRepository->findBy($criteria),
            $request->query->getInt('page', 1),
            5
        );

        $tachesTerminees = $tachesRepository->count(['user' => $user, 'statut' => 'Terminée']);
        $tachesEnCours = $tachesRepository->count(['user' => $user, 'statut' => 'En cours']);
        $tachesAFaire = $tachesRepository->count(['user' => $user, 'statut' => 'À faire']);
        $totalTaches = $tachesRepository->count(['user' => $user]);

        return $this->render('taches/index.html.twig', [
            'taches' => $taches,
            'taches_terminees' => $tachesTerminees,
            'taches_en_cours' => $tachesEnCours,
            'taches_a_faire' => $tachesAFaire,
            'total_taches' => $totalTaches
        ]);
    }

    #[Route('/new', name: 'app_taches_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tach = new Taches();
        $form = $this->createForm(TachesType::class, $tach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tach->setUser($this->getUser());
            $entityManager->persist($tach);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre tache a été créé avec succès !'
            );

            return $this->redirectToRoute('app_taches_index');
        }


        return $this->render('taches/new.html.twig', [
            'tach' => $tach,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_taches_show', methods: ['GET'])]
    public function show(Taches $tach): Response
    {
        if ($tach->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('taches/show.html.twig', [
            'tach' => $tach,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_taches_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Taches $tach, EntityManagerInterface $entityManager): Response
    {
    {
        if ($tach->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(TachesType::class, $tach);
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Votre tache a été modifiée avec succès !'
                );

                return $this->redirectToRoute('app_taches_index');
            }


        return $this->render('taches/edit.html.twig', [
            'tach' => $tach,
            'form' => $form,
        ]);
    }
    }

     #[Route('/{id}', name: 'app_taches_delete', methods: ['POST'])]
    public function delete(Request $request, Taches $tach, EntityManagerInterface $entityManager): Response
    {
        if ($tach->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $tach->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tach);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre tache a été supprimée avec succès !'
            );

            return $this->redirectToRoute('app_taches_index');
        }

}
}
