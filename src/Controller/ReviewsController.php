<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Review;
use App\Form\ReviewFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewsController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/reviews/create/{slug}', name: 'reviews_create', methods: ["GET", "POST"])]
    public function create($slug, Request $request): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewFormType::class, $review);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newReview = $form->getData();

            $newReview->setUser($this->getUser());

            $moviesRepository = $this->em->getRepository(Movie::class);
            $newReview->setMovie($moviesRepository->findOneBy(['slug' => $slug]));

            $this->em->persist($newReview);
            $this->em->flush();

            return $this->redirectToRoute('movies_show', [
                'slug' => $slug
            ]);
        }

        return $this->render('reviews/create.php.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/reviews/delete/{id}', name: 'reviews_delete', methods: ["POST"])]
    public function delete($id, Request $request): Response {
        $referer = $request->headers->get('referer');

        if (!$this->isCsrfTokenValid('review_delete', $request->getPayload()->get('_csrf_token'))) {
            return $this->redirect($referer);
        }

        $repository = $this->em->getRepository(Review::class);

        $review = $repository->find($id);
        $this->em->remove($review);
        $this->em->flush();

        return $this->redirect($referer);
    }
}
