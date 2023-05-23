<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class B64Controller extends AbstractController
{
    #[Route('/b64', name: 'app_b64')]
    public function index(): Response
    {
        return $this->render('b64/index.html.twig', [
            'controller_name' => 'B64Controller',
        ]);
    }
}
