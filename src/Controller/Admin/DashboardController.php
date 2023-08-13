<?php

namespace App\Controller\Admin;

use App\Entity\Taches;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Repository\TachesRepository;
use Doctrine\ORM\EntityManagerInterface;

class DashboardController extends AbstractDashboardController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès refusé.');
        }

        $user = $this->getUser();
        $tachesRepository = $this->entityManager->getRepository(Taches::class);

        function filterAndCountTasks($taches, $statusFilter, $userFilter)
        {
            return count(array_filter($taches, function ($tache) use ($statusFilter, $userFilter) {
                return $tache->getStatut() === $statusFilter && $tache->getUser() !== $userFilter;
            }));
        }

        $allTaches = $tachesRepository->findBy([], ['id' => 'DESC']);

        $tachesTerminees = filterAndCountTasks($allTaches, 'Terminée', $user);
        $tachesEnCours = filterAndCountTasks($allTaches, 'En cours', $user);
        $tachesAFaire = filterAndCountTasks($allTaches, 'À faire', $user);
        $totalTaches = count(array_filter($allTaches, function ($tache) use ($user) {
            return $tache->getUser() !== $user;
        }));

        $tachesAdmin = count(array_filter($allTaches, function ($tache) use ($user) {
            return $tache->getUser() === $user;
        }));

        return $this->render('admin/dashboard.html.twig', [
            'taches_terminees' => $tachesTerminees,
            'taches_en_cours' => $tachesEnCours,
            'taches_a_faire' => $tachesAFaire,
            'total_taches' => $totalTaches,
            'latest_taches' => array_slice($allTaches, 0, 5),
            'taches_admin' => $tachesAdmin,
            'user' => $user,
        ]);

    }
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('to_do_list - Administration')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Taches', 'fas fa-list', Taches::class);
    }
}
