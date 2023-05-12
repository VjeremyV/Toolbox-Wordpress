<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardAccueilController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard_accueil')]
    public function index(): Response
    {
        return $this->render('dashboard_accueil/index.html.twig', [
            'controller_name' => 'DashboardAccueilController',
       
        ]);
    }
}
