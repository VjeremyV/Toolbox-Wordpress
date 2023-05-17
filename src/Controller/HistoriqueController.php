<?php

namespace App\Controller;

use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoriqueController extends AbstractController
{
    #[Route('/historique/getSites/', name: 'app_historique_getSites', methods: ['POST'])]
    public function getSites(SiteRepository $siteRepository): Response
    {
        $user = $this->getUser();
        if($user){
        $result = [];
        foreach($siteRepository->findAll() as $site){
            $result[] = $site->getNom();
        }
        return new Response(content: json_encode($result));
    }

    return new Response(content: json_encode(['WRONG USER' => 'WRONG USER MESSAGE']));
    }

    
}
