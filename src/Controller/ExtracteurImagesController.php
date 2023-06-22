<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Classes\FormChecker;
use App\Classes\File;
use App\Entity\Date;
use App\Entity\Fichier;
use App\Entity\Site;
use App\Entity\Users;
use App\Repository\DateRepository;
use App\Repository\FichierRepository;
use App\Repository\OutilsRepository;
use App\Repository\SiteRepository;
use DateTime;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[IsGranted('ROLE_USER')]
class ExtracteurImagesController extends AbstractController
{
    #[Route('/extracteur-images', name: 'app_extracteur_images')]
    public function index(Request $request, SiteRepository $siteRepository): Response
    {
        // on créée le formulaire
        $form = self::displayFileForm();
        //on vérifie la request pour saovir si le formulaire a été soumis
        $form->handleRequest($request);
        // si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $_SESSION['spinnerman']['last_spinned_blog'] = $form['nom_blog']->getData();
            if(AsynchroneController::is_new($form['nom_blog']->getData(), $siteRepository)){
                $site = new Site();
                $site->setNom($form['nom_blog']->getData());
                $site->setUrl("");
                $siteRepository->save($site, true);
            }
            // on instancie un nouveau formChecker
            $checker = new FormChecker();
            // on récupère la data du champs contenant le fichier
            $file = $form['xmlArticlesWP']->getData();

            // si la data passe le check de sécurité
            if ($checker->check_file_extension($file->getClientOriginalExtension(), ['xml'])) {
                // on instancie un nouvel object file pour manipuler la data
                $displayerXml = new File();
                // on renvoie les données du xml triées
                $xml = $displayerXml->display_xml($file);

                $imgs_urls = [];
            }
        } else {
            $imgs_urls = "";
            $xml = "";
        }
        return $this->render('extracteur_images/extracteur.html.twig', [
            'form' => $form->createView(),
            'xml' => $xml,
            'imgs_urls' => $imgs_urls,
        ]);
    }

    #[Route('/extracteur-images/historique', name: 'app_extracteur_historique')]
    public function index_historique(FichierRepository $fichierRepository, SiteRepository $siteRepository, DateRepository $dateRepository, request $request): Response
    {
        $fichiers_infos = $fichierRepository->findBy(['outils' => 1]);
        $fichiers = [];
        $query = $request->query;
        $search = [];
        foreach ($query as $req => $value) {
            if ($req != 'offset' && $req != "type" && $req != "rechercher") {
                $search[] = $req;
            } elseif ($req === "type" || $req ==="rechercher"){
                $search[] = $value;
            }
        }
        /************Pagination ********************/
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $fichierRepository->getFichierPaginatorExtracteurImg($offset, count($search) > 0 ? $search : null);
        $previous = $offset - FichierRepository::PAGINATOR_PER_PAGE;
        $next = min(count($paginator), $offset + FichierRepository::PAGINATOR_PER_PAGE);
        $nbrePages = ceil(count($paginator) / FichierRepository::PAGINATOR_PER_PAGE);
        $pageActuelle = ceil($next / FichierRepository::PAGINATOR_PER_PAGE);
        $difPages = $nbrePages - $pageActuelle;
        ////////////////////////////////////////////////////////////////////////////
        foreach ($fichiers_infos as $index => $fichier_info) {
            $fichiers[$index]["fichier"] = $fichier_info;
            $fichiers[$index]["site"] = $siteRepository->findOneBy(['id' => $fichier_info->getSite()]);
            $fichiers[$index]["Date"] = $dateRepository->findOneBy(['id' => $fichier_info->getDate()]);
        }

        return $this->render('extracteur_images/historique.html.twig', [
            'fichiers' => $paginator,
            'previous' => $previous,
            'next' => $next,
            'nbrePages' => $nbrePages,
            'pageActuelle' => $pageActuelle,
            'difPages' => $difPages,
            "offset" => $offset,
            'query' => $search
        ]);
    }

    #[Route('/extracteur-images/supp/{id}', name: 'app_extracteur-images_filesupp')]
    public function admin_supp(FichierRepository $fichierRepository, Fichier $fichier): Response
    {
        $outil= $fichier->getOutils();
        if($outil->getId() === 2){

            $fichierRepository->remove($fichier, true);
            
            $this->addFlash('info', 'Le fichier a bien été supprimé');
            return $this->redirectToRoute('app_extracteur_historique');
        }
        return $this->redirectToRoute('app_extracteur_historique');

    }


    /**
     * affiche le formulaire pour envoyer son fichier xml
     *
     * @return object
     */
    private function displayFileForm(): object
    {
        return $this->createFormBuilder(null, ['attr' => ['id' => 'entryFile', 'autocomplete' => 'off' ]])
            ->add('nom_blog', TextType::class, ['label' => 'Nom du blog'])
            ->add('xmlArticlesWP', FileType::class, [
                'label' => 'Telecharger votre fichier export ici',
                'required' => true,
                'mapped' => false,

            ])
            ->add('envoi', SubmitType::class, [
                'label' => 'Envoyer à l\'extracteur', 'attr' => ['class' => 'btn btn-lg btn-primary mt-3 allWaButton']
            ])
            ->setMethod('POST')
            ->getForm();
    }
}
