<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Review;
use App\Form\ReviewFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewsController extends AbstractController
{

    public function __construct(protected EntityManagerInterface $em)
    {
    }

    #[Route('/reviews/create/{slug}', name: 'reviews_create', methods: ["GET", "POST"])]
    #[IsGranted('IS_AUTHENTICATED')]
    public function create(
        $slug, 
        Request $request
    ): Response {
        $review = new Review();
        $form = $this->createForm(ReviewFormType::class, $review);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newReview = $form->getData();

            $newReview->setUser($this->getUser());

            $moviesRepository = $this->em->getRepository(Movie::class);
            $movie = $moviesRepository->findOneBy(['slug' => $slug]);
            $newReview->setMovie($movie);

            $this->em->persist($newReview);
            $this->em->flush();

            return $this->redirectToRoute('movies_show', [
                'slug' => $movie->getSlug(),
            ]);
        }

        return $this->render('reviews/create.php.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/reviews/edit/{id}', name: 'reviews_edit', methods: ["GET", "POST"])]
    #[IsGranted('IS_AUTHENTICATED')]
    public function edit(
        $id, 
        Request $request
    ): Response {
        $repository = $this->em->getRepository(Review::class);
        $review = $repository->find($id);

        $form = $this->createForm(ReviewFormType::class, $review);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $review->setHeading($form->get('heading')->getData());
            $review->setReview($form->get('review')->getData());
            $review->setContent($form->get('content')->getData());

            $this->em->persist($review);
            $this->em->flush();

            $page = $request->query->getInt('page', 1);
            $reviews = $repository->findAllReviews($page, 3);

            return $this->render('dashboard/reviews.php.twig', [
                'reviews' => $reviews
            ]);
        }

        return $this->render('reviews/create.php.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/reviews/delete/{id}', name: 'reviews_delete', methods: ["POST"])]
    #[IsGranted('IS_AUTHENTICATED')]
    public function delete(
        $id, 
        Request $request
    ): Response {
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
