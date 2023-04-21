<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PluginsController extends AbstractController
{
    #[Route('/plugins', name: 'app_plugins')]
    public function index(): Response
    {
        return $this->render('plugins/plugin.html.twig');
    }
}
