<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Review;
use App\Entity\User;
use App\Entity\Actor;
use App\Form\UserProfileFormType;
use App\Form\UserRoleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DashboardController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/dashboard', name: 'dashboard_index')]
    #[isGranted('IS_AUTHENTICATED')]
    public function index(): Response
    {
        $repository = $this->em->getRepository(Movie::class);

        return $this->render('dashboard/index.html.twig', [
            'moviesCount' => $repository->count([]),
            'newMovies' => $repository->findBy([], ['id' => 'DESC'], 5, 0),
            'reviewsCount' => $this->em->getRepository(Review::class)->count(['user' => $this->getUser()]),
            'usersCount' => $this->em->getRepository(User::class)->count([]),
        ]);
    }

    #[Route('/dashboard/movies', name: 'dashboard_movies')]
    #[isGranted('IS_AUTHENTICATED')]
    public function movies(Request $request): Response
    {   
        $repository = $this->em->getRepository(Movie::class);

        return $this->render('dashboard/movies.php.twig', [
            'movies' => $repository->findAllMovies($request->query->getInt('page',1), 3),
        ]);
    }

    #[Route('/dashboard/admin/users', name: 'dashboard_admin_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function users(Request $request): Response
    {
        $repository = $this->em->getRepository(User::class);
        
        return $this->render('dashboard/admin/users.php.twig', [
            'users' => $repository->findAllUsers($request->query->getInt('page',1), 3)
        ]);
    }

    #[Route('/dashboard/admin/movies', name: 'dashboard_admin_movies')]
    #[IsGranted('ROLE_EDITOR')]
    public function admin_movies(Request $request): Response
    {
        $repository = $this->em->getRepository(Movie::class);

        return $this->render('dashboard/admin/movies.php.twig', [
            'movies' => $repository->findAllMovies($request->query->getInt('page',1), 3)
        ]);
    }

    #[Route('/dashboard/admin/reviews', name: 'dashboard_admin_reviews')]
    #[IsGranted('ROLE_EDITOR')]
    public function admin_reviews(Request $request): Response
    {
        $repository = $this->em->getRepository(Review::class);

        return $this->render('dashboard/admin/reviews.php.twig', [
            'reviews' => $repository->findAllReviews($request->query->getInt('page',1), 3)
        ]);
    }

    #[Route('/dashboard/admin/roles/{id}', name: 'dashboard_admin_roles')]
    #[IsGranted('ROLE_ADMIN')]
    public function admin_roles($id, Request $request): Response
    {
        $repository = $this->em->getRepository(User::class);
        $user = $repository->find($id);

        $form = $this->createForm(UserRoleFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles($form->get('roles')->getData());

            $this->em->flush();
        }

        return $this->render('dashboard/admin/roles.php.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/dashboard/actors', name: 'dashboard_actors')]
    #[IsGranted('ROLE_EDITOR')]
    public function actors(Request $request): Response
    {
        $repository = $this->em->getRepository(Actor::class);
        $page = $request->query->getInt('page', 1);
        $actors = $repository->findAllActors($page, 3);

        return $this->render('dashboard/actors.php.twig', [
            'actors' => $actors
        ]);
    }

    #[Route('/dashboard/reviews', name: 'dashboard_reviews')]
    #[isGranted('IS_AUTHENTICATED')]
    public function reviews(Request $request): Response
    {
        $repository = $this->em->getRepository(Review::class);
        $page = $request->query->getInt('page', 1);
        $reviews = $repository->findAllByUser($this->getUser()->getId(), $page, 3);

        return $this->render('dashboard/reviews.php.twig', [
            'reviews' => $reviews
        ]);
    }

    #[Route('/dashboard/profile', name: 'dashboard_profile')]
    #[isGranted('IS_AUTHENTICATED')]
    public function profile(Request $request): Response
    {
        $repository = $this->em->getRepository(User::class);
        $user = $repository->findOneBy(['id' => $this->getUser()->getId()]);

        $form = $this->createForm(UserProfileFormType::class, $user);

        $form->handleRequest($request);

        $imagePath = $form->get('imagePath')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($imagePath) {
                if ($user->getImagePath() !== null) {
                    if (file_exists($this->getParameter('kernel.project_dir') . $user->getImagePath())) {
                        $this->getParameter('kernel.project_dir') . $user->getImagePath();

                        $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                        try {
                            $imagePath->move(
                                $this->getParameter('kernel.project_dir') . '/public/uploads',
                                $newFileName
                            );
                        } catch (FileException $e) {
                            return new Response($e->getMessage());
                        }

                        $user->setImagePath('/uploads/' . $newFileName);

                        $this->em->flush();

                        return $this->redirectToRoute('dashboard_profile');
                    }
                }
            } else {
                $user->setName($form->get('name')->getData());
                $user->setEmail($form->get('email')->getData());
                $user->setBio($form->get('bio')->getData());

                $this->em->flush();
                return $this->redirectToRoute('dashboard_profile');
            }
        }

        return $this->render('dashboard/profile.php.twig', [
            'form' => $form->createView()
        ]);
    }
}
