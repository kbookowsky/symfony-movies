<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{

    #[Route('/dashboard', name: 'dashboard_index')]
    public function index(): Response
    {   
        if (!in_array('ROLE_ADMIN',$this->getUser()->getRoles(), true)) {
            return $this->render('movies/index.html.twig');
        }
        return $this->render('dashboard/index.html.twig');
    }

    #[Route('/dashboard/movies', name: 'dashboard_movies')]
    public function movies(): Response
    {   
        if (!in_array('ROLE_ADMIN',$this->getUser()->getRoles(), true)) {
            return $this->render('movies/index.html.twig');
        }
        return $this->render('dashboard/movies.html.twig');
    }

    #[Route('/dashboard/users', name: 'dashboard_users')]
    public function users(): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            return $this->render('movies/index.html.twig');
        }
        return $this->render('dashboard/users.html.twig');
    }
}
