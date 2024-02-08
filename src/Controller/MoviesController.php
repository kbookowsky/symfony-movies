<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{

    public function __construct(protected EntityManagerInterface $em)
    {
    }

    #[Route('/', name: 'movies_index', methods: ["GET"])]
    public function index(Request $request): Response
    {
        $repository = $this->em->getRepository(Movie::class);
        $movies = $repository->findAll();

        return $this->render('movies/index.html.twig', [
            'movies' => $movies
        ]);
    }

    #[Route('/movie/{slug}', name: 'movies_show', methods: ["GET"] )]
    public function show($slug): Response
    {
        $repository = $this->em->getRepository(Movie::class);
        $movie = $repository->findOneBy(['slug' => $slug]);
        return $this->render('movies/show.html.twig', [
            'movie' => $movie
        ]);
    }

    #[Route('/movies/create', name: 'movies_create', methods: ["GET", "POST"])]
    #[IsGranted('ROLE_EDITOR')]
    public function create(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newMovie = $form->getData();

            $lastMovie = $this->em->getRepository(Movie::class)->findBy([], ['id' => 'DESC'], 1, 0);
            $guessID = 0;

            if ($lastMovie) {
                $guessID = $lastMovie[0]->getId() + 1;
            }

            $slug = str_replace(' ', '-', strtolower($newMovie->getTitle())) . '-' . $newMovie->getReleaseYear() . '-' . $guessID;

            $newMovie->setSlug($slug);

            $imagePath = $form->get('imagePath')->getData();
            if ($imagePath) {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $newMovie->setImagePath('/uploads/' . $newFileName);
            }

            $this->em->persist($newMovie);
            $this->em->flush();

            return $this->redirectToRoute('movies_index');
        }

        return $this->render('movies/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/movies/edit/{slug}', name: 'movies_edit', methods: ["GET", "POST"] )]
    #[IsGranted('ROLE_EDITOR')]
    public function edit(
        $slug, 
        Request $request
    ): Response {
        $repository = $this->em->getRepository(Movie::class);
        $movie = $repository->findOneBy(['slug' => $slug]);

        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        $imagePath = $form->get('imagePath')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if (
                $imagePath && 
                $movie->getImagePath() !== null && 
                file_exists($this->getParameter('kernel.project_dir') . $movie->getImagePath())
            ) {
                $this->getParameter('kernel.project_dir') . $movie->getImagePath();

                $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $movie->setImagePath('/uploads/' . $newFileName);

                $this->em->flush();

                return $this->redirectToRoute('movies_index');
            }

            $movie->setTitle($form->get('title')->getData());
            $movie->setReleaseYear($form->get('releaseYear')->getData());
            $movie->setDescription($form->get('description')->getData());

            $this->em->flush();
            return $this->redirectToRoute('movies_index');
        }

        return $this->render('movies/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView()
        ]);
    }

    #[Route('/movies/delete/{slug}', name: 'movies_delete', methods: ["POST"])]
    #[IsGranted('ROLE_EDITOR')]
    public function delete(
        $slug, 
        Request $request
    ): Response {
        $referer = $request->headers->get('referer');

        if (!$this->isCsrfTokenValid('movie_delete', $request->getPayload()->get('_csrf_token'))) {
            return $this->redirect($referer);
        }

        $repository = $this->em->getRepository(Movie::class);

        $movie = $repository->findOneBy(['slug'=> $slug]);
        $this->em->remove($movie);
        $this->em->flush();

        return $this->redirect($referer);
    }
}
