<?php

namespace App\Controller;

use App\Entity\Site;
use App\Repository\DateRepository;
use App\Repository\ElementsRepository;
use App\Repository\FichierRepository;
use App\Repository\SiteRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoriqueController extends AbstractController
{
    #[Route('/historique/getSites/', name: 'app_historique_getSites', methods: ['POST'])]
    public function getSites(SiteRepository $siteRepository): Response
    {
        $user = $this->getUser();
        if ($user) {
            $result = [];
            foreach ($siteRepository->findAll() as $site) {
                $result[] = $site->getNom();
            }
            return new Response(content: json_encode($result));
        }

        return new Response(content: json_encode(['WRONG USER' => 'WRONG USER MESSAGE']));
    }

    #[Route('/sites', name: 'app_sites')]
    public function index(SiteRepository $siteRepository, Request $request): Response
    {
        $sites = $siteRepository->findAll();

        /************Pagination ********************/
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $siteRepository->getFichierPaginator($offset);
        $previous = $offset - SiteRepository::PAGINATOR_PER_PAGE;
        $next = min(count($paginator), $offset + SiteRepository::PAGINATOR_PER_PAGE);
        $nbrePages = ceil(count($paginator) / SiteRepository::PAGINATOR_PER_PAGE);
        $pageActuelle = ceil($next / SiteRepository::PAGINATOR_PER_PAGE);
        $difPages = $nbrePages - $pageActuelle;
        ////////////////////////////////////////////////////////////////////////////

        return $this->render('Sites/index.html.twig', [
            'sites' => $paginator,
            'previous' => $previous,
            'next' => $next,
            'nbrePages' => $nbrePages,
            'pageActuelle' => $pageActuelle,
            'difPages' => $difPages,
            "offset" => $offset,
        ]);
    }


    #[Route('/sites/supp/{id}', name: 'app_sites_filesupp')]
    public function admin_supp(SiteRepository $siteRepository, Site $site, FichierRepository $fichierRepository, ElementsRepository $elementsRepository, DateRepository $dateRepository): Response
    {
        $fichiers = $fichierRepository->findBy(['site' => $site->getId()]);
        $dates = [];
        $elements = [];
        foreach ($fichiers as $fichier) {
            $dates[] = $fichier->getdate();
            $elements[] = $fichier->getElements();
        }
        try{
            foreach ($elements as $listeElement) {
                foreach($listeElement as $element){
                    $elementsRepository->remove($element, true);
                }
        }
        foreach ($fichiers as $fichier) {
            $fichierRepository->remove($fichier, true);
        }
        foreach ($dates as $date) {
            $inBdd[] = $fichierRepository->findBy(['date' => $date]);
            if (count($inBdd) < 1) {
                $dateRepository->remove($date, true);
            }
        }
        $siteRepository->remove($site, true);
    } catch(Exception $err){
        return $err;
    }

    return $this->redirectToRoute('app_sites');

    }
}
