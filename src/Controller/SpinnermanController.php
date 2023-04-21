<?php

namespace App\Controller;

use App\Classes\FormChecker;
use App\Classes\File;
use App\Repository\DateRepository;
use App\Repository\FichierRepository;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Site;

class SpinnermanController extends AbstractController
{
    #[Route('/spinnerman', name: 'app_spinnerman')]
    public function index(Request $request, SiteRepository $siteRepository): Response
    {
        // on créée le formulaire
        $NewFileForm = self::displayNewFileForm();
        //on vérifie la request pour saovir si le formulaire a été soumis
        $NewFileForm->handleRequest($request);
        $xml = self::formTreatment($NewFileForm, $siteRepository);
        $sites = $siteRepository->findAll();

        return $this->render('spinnerman/spinnerman.html.twig', [
            'NewFileForm' => $NewFileForm->createView(),
            'xml' => $xml,
            'sites' => $sites
                ]);
    }
    #[Route('/spinnerman/existing_blog', name: 'app_spinnerman_existing_blog')]
    public function index_existing_blog(Request $request, SiteRepository $siteRepository): Response
    {

        $form = self::displayExistingwFileForm($this->getListSites($siteRepository));
        //on vérifie la request pour saovir si le formulaire a été soumis
        $form->handleRequest($request);
        $xml = self::formTreatment($form);

        return $this->render('spinnerman/spinnerman-existing.html.twig', [
            'form' => $form->createView(),
            'xml' => $xml,
                ]);
    }

   

    private function getListSites(SiteRepository $siteRepository){
        $sites_infos = $siteRepository->findAll();
        $sites = [];
        foreach ($sites_infos as $site) {
            $sites[$site->getNom()]= $site->getId();  // on construit le tableau de cette manière pour les les <options> du <select> soit au format <option value='idDuSite'>nomDuSite</option> 
        }
      return  $sites;
    }
    /**
     * affiche le formulaire pour envoyer son fichier xml pour un nouveau blog
     *
     * @return object
     */
    private function displayNewFileForm(): object
    {
        return $this->createFormBuilder(null, ['attr' => ['id' => 'entryFile', 'autocomplete' => 'off']])
            ->add('nom_blog', TextType::class, ['label' => 'Nom du blog'])
            ->add('xmlArticlesWP', FileType::class, [
                'label' => 'Telecharger votre fichier export ici',
                'required' => true,
                'mapped' => false,

            ])
            ->add('envoi', SubmitType::class, [
                'label' => 'Envoyer à spinnerman', 'attr' => ['class' => 'btn btn-lg btn-primary mt-3 allWaButton']
            ])
            ->setMethod('POST')
            ->getForm();
    }
    /**
     * affiche le formulaire pour envoyer son fichier xml pour un blog existant
     *
     * @return object
     */
    private function displayExistingwFileForm(array $list_sites): object
    {
        return $this->createFormBuilder(null, ['attr' => ['id' => 'entryFileExisting', 'autocomplete' => 'off']])
            ->add('nom_blogExisting', TextType::class, ['label' => 'Nom du blog'])
            ->add('envoiExisting', SubmitType::class, [
                'label' => 'Envoyer à spinnerman', 'attr' => ['class' => 'btn btn-lg btn-primary mt-3 allWaButton']
            ])
            ->setMethod('POST')
            ->getForm();
    }

    private function formTreatment(object $form)
    {
        if ($form->isSubmitted() && $form->isValid()) {
            // on fait la différence entre les 2 formulaire grâce à l'existente ou non des variables du premier formulaire
            $_SESSION['spinnerman']['last_spinned_blog'] = isset($form['nom_blog']) ? $form['nom_blog']->getData() : $form['nom_blogExisting']->getData() ; 
           
            // on utilise la class FormChecker pour vérifier l'extension du fichier envoyé
            $checker = new FormChecker();
            $file = isset($form['xmlArticlesWP']) ? $form['xmlArticlesWP']->getData() : $form['xmlArticlesWPExisting']->getData();

            if ($checker->check_file_extension($file->getClientOriginalExtension(), ['xml'])) {
                
                $displayerXml = new File();
                return $displayerXml->display_xml($file); //retourne un array depuis le fichier xml
            }
        }
        return "";
    }

}
