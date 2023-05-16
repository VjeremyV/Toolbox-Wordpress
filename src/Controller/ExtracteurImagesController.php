<?php

namespace App\Controller;

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
