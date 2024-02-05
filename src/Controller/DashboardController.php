<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Review;
use App\Entity\User;
use App\Entity\Actor;
use App\Form\UserProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DashboardController extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/dashboard', name: 'dashboard_index')]
    public function index(): Response
    {
        if (!in_array('ROLE_ADMIN',$this->getUser()->getRoles(), true)) {
            return $this->render('movies/index.html.twig');
        }

        $repository = $this->em->getRepository(Movie::class);

        return $this->render('dashboard/index.html.twig', [
            'moviesCount' => $repository->count([]),
            'newMovies' => $repository->findBy([], ['id' => 'DESC'], 5, 0),
            'reviewsCount' => $this->em->getRepository(Review::class)->count(['user' => $this->getUser()]),
            'usersCount' => $this->em->getRepository(User::class)->count([]),
        ]);
    }

    #[Route('/dashboard/movies', name: 'dashboard_movies')]
    #[IsGranted('ROLE_ADMIN')]
    public function movies(Request $request): Response
    {   
        $repository = $this->em->getRepository(Movie::class);

        return $this->render('dashboard/movies.php.twig', [
            'movies' => $repository->findAllPosts($request->query->getInt('page',1), 3),
        ]);
    }

    #[Route('/dashboard/admin/users', name: 'dashboard_admin_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function users(Request $request): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            return $this->render('movies/index.html.twig');
        }

        $repository = $this->em->getRepository(User::class);
        
        return $this->render('dashboard/admin/users.php.twig', [
            'users' => $repository->findAllPosts($request->query->getInt('page',1), 3)
        ]);
    }

    #[Route('/dashboard/admin/movies', name: 'dashboard_admin_movies')]
    #[IsGranted('ROLE_ADMIN')]
    public function admin_movies(Request $request): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            return $this->render('movies/index.html.twig');
        }

        $repository = $this->em->getRepository(Movie::class);

        return $this->render('dashboard/admin/movies.php.twig', [
            'movies' => $repository->findAllPosts($request->query->getInt('page',1), 3)
        ]);
    }

    #[Route('/dashboard/admin/reviews', name: 'dashboard_admin_reviews')]
    #[IsGranted('ROLE_ADMIN')]
    public function admin_reviews(Request $request): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            return $this->render('movies/index.html.twig');
        }

        $repository = $this->em->getRepository(Review::class);

        return $this->render('dashboard/admin/reviews.php.twig', [
            'reviews' => $repository->findAllPosts($request->query->getInt('page',1), 3)
        ]);
    }

    #[Route('/dashboard/actors', name: 'dashboard_actors')]
    #[IsGranted('ROLE_ADMIN')]
    public function actors(Request $request): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            return $this->render('movies/index.html.twig');
        }

        $repository = $this->em->getRepository(Actor::class);
        $page = $request->query->getInt('page', 1);
        $actors = $repository->findAllPosts($page, 3);

        return $this->render('dashboard/actors.php.twig', [
            'actors' => $actors
        ]);
    }

    #[Route('/dashboard/reviews', name: 'dashboard_reviews')]
    #[IsGranted('ROLE_USER')]
    public function reviews(Request $request): Response
    {
        if (!in_array('ROLE_USER', $this->getUser()->getRoles(), true)) {
            return $this->render('movies/index.html.twig');
        }

        $repository = $this->em->getRepository(Review::class);
        $page = $request->query->getInt('page', 1);
        $reviews = $repository->findAllPosts($page, 3);

        return $this->render('dashboard/user-reviews.php.twig', [
            'reviews' => $reviews
        ]);
    }

    #[Route('/dashboard/profile', name: 'dashboard_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(Request $request): Response
    {
        if (!in_array('ROLE_USER', $this->getUser()->getRoles(), true)) {
            return $this->render('movies/index.html.twig');
        }

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
