<?php

namespace App\Controller;

use App\Entity\Site;
use App\Repository\DateRepository;
use App\Repository\ElementsRepository;
use App\Repository\FichierRepository;
use App\Repository\SiteRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoriqueController extends AbstractController
{
  

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


    #[Route('/changeUrl/{id}', name: 'app_changeUrl', requirements: ["id"=>"\d+"])]
    public function changeUrl(Site $Site, SiteRepository $siteRepository, Request $request): Response
    {
      $form = self::displayChangeUrlForm();
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $Site->setUrl($form['URL']->getData());
        $siteRepository->save($Site, true);
        $this->addFlash('info', 'l\'URL a bien été enregistrée');
      }
        ////////////////////////////////////////////////////////////////////////////

        return $this->render('Sites/changeUrl.html.twig', [
          'form'=> $form->createView()
        ]);
    }

   /**
     * affiche le formulaire pour envoyer son fichier xml pour un nouveau blog
     *
     * @return object
     */
    private function displayChangeUrlForm(): object
    {
        return $this->createFormBuilder(null, ['attr' => ['autocomplete' => 'off']])
            ->add('URL', TextType::class, ['label' => 'Renseigner l\'URL du Site'])
            ->add('envoyer', SubmitType::class, [
                'label' => 'Soumettre', 'attr' => ['class' => 'btn btn-lg btn-primary mt-3 allWaButton']
            ])
            ->setMethod('POST')
            ->getForm();
    }

}
