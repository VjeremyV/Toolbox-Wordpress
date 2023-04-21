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
use App\Repository\FichierRepository;
use App\Repository\OutilsRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ExtracteurImagesController extends AbstractController
{
    #[Route('/extracteur-images', name: 'app_extracteur_images')]
    public function index(Request $request): Response
    {
        // on créée le formulaire
        $form = self::displayFileForm();
        //on vérifie la request pour saovir si le formulaire a été soumis
        $form->handleRequest($request);
        // si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $_SESSION['spinnerman']['last_spinned_blog'] = $form['nom_blog']->getData();
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
                // $urls_dossier = self::format_urls_directory($xml);
                // foreach ($urls_dossier as $url => $directories) {
                //     $file_name = array_pop($directories);
                //     $displayerXml->download_file($url, $directories, 'test', $file_name);
                // }
            }
        } else {
            $imgs_urls = "";
            $xml = "";
        }
        return $this->render('extracteur_images/extracteur.html.twig', [
            'form' => $form->createView(),
            'xml' => $xml,
            'imgs_urls' => $imgs_urls,
            // 'dossier' => $urls_dossier
        ]);
    }


    #[Route('/dl_file/{nom_blog}', name: 'app_dlf', methods: ['POST'])]
    public function dl_file(string $nom_blog): Response
    {
        $entityBody = file_get_contents('php://input');
        $body = json_decode($entityBody, true);

        $file = new File();

        $urls_dossier = self::format_urls_directory($body);

        foreach ($urls_dossier as $url => $directories) {
            $file_name = array_pop($directories);
            $file->download_file($url, $directories, $nom_blog, $file_name);
        }

        return new Response(content: json_encode($urls_dossier));
    }


    #[Route('/make_zip/{nom_blog}', name: 'app_mkzip', methods: ['POST'])]
    public function make_zip(string $nom_blog): Response
    {
        $entityBody = file_get_contents('php://input');
        $body = json_decode($entityBody, true);
        $zip = new File();
        $response = $zip->make_zip($body, $nom_blog);
      
        
        return new Response(content: json_encode(['zipFile'=> $response]));
    }


/**
 * formate les urls des dossiers pour utiliser la fonction de telechargement
 *
 * @param array $urls
 * @return array
 */
    private function format_urls_directory(array $urls): array
    {
        foreach ($urls as $post) {
            foreach ($post as $element) {
                // $imgs_urls[$post['h1']] =  self::pick_img_url($post['contenu']);
                foreach (self::pick_img_url($element['contenu']) as $urls) {
                    foreach ($urls as $url) {
                        $image_url = explode("/", $url);
                        for ($i = 0; $i < count($image_url); $i++) {
                            if ($i >= count($image_url) - 3) {
                                $urls_dossier[$url][] = $image_url[$i];
                            }
                        }
                    }
                }
            }
        }
        return $urls_dossier;
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

    private function pick_img_url(string $html)
    {

        $extract_list[] = explode('<img', $html); // on éclate les différentes chaines de caractères correspondant aux posts à chaque fois qu'une balise image s'ouvre

        $list_imgs = [];

        $count = 0; // on initie un compteur
        foreach ($extract_list as $html_parts) {
            array_shift($html_parts); // à chaque tableau contenant les différents éléments html du post, le premier élément ne contient pas d'image donc on le supprime
            foreach ($html_parts as $string) {
                $post_array = explode('/>', $string); //la string contenant le html du post
                $img_html = $post_array[0]; //la string contenant le html de l'image
                $img_metas = explode('"', $img_html); // on sépare les éléments de la string en un tableau
                $index_of_src = array_search(" src=", $img_metas); //on recherche l'index de la string contenant " src=" car on sait qu'elle précède l'url de l'image
                $list_imgs[$count][] = $img_metas[$index_of_src + 1]; //on ajoute au tableau de résultat l'url dont l'index correspond àindex_of_src +1
            }
            $count++; // on incrémente le compteur
        }

        return $list_imgs; // on retourne la liste des urls des images
    }

}
