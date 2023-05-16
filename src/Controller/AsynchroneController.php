<?php

namespace App\Controller;

use App\Classes\File;
use App\Classes\TranslatorWa;
use App\Entity\Date;
use App\Entity\Fichier;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Site;
use App\Repository\DateRepository;
use App\Repository\FichierRepository;
use App\Repository\OutilsRepository;
use DateTime;

class AsynchroneController extends AbstractController
{
    #[Route('/translate/{nom_blog}', name: 'app_translate', methods: ['POST'])]
    public function index(string $nom_blog, Request $request, SiteRepository $siteRepository, FichierRepository $fichierRepository, DateRepository $dateRepository, OutilsRepository $outilsRepository): Response
    {
        $user = $this->getUser();
        if($user){
        //on récupère le contenu du xml et on converti le json en objet itérable
        // php://input = flux en lecture seule qui vous permet de lire les données brutes du corps de la requête.
        $entityBody = file_get_contents('php://input');
        $body = json_decode($entityBody);
        $translator = new TranslatorWa();
        $file = new File();
        $result = []; //tableau des resultats qui sera retourné
        $count = 0;
        $txtFile = []; //tableau qui contiendra les données à inscrire dans le fichier txt

        //on vérifie si le site n'existe pas déjà en bdd, sinon on le créée
        if (self::is_new($nom_blog, $siteRepository)) {
            $site = new Site();
            $site->setNom($nom_blog);
            $site->setUrl("");
            $siteRepository->save($site, true);
        } else {
            $site = $siteRepository->findBy(['nom' => $nom_blog])[0];
        }


        // Pour chaque post du corp de la requete
        foreach ($body as $post) {
            foreach ($post as $element => $value) {
                //si l'élément n'est pas la date
                if ($element != "date") {
                    // si l'élément est l'url on transforme l'URL en slug pour la traduction
                    if ($element === "url") {
                        $value = self::urlToSlug($value);
                    }
                    //On traduit la valeur en anglais
                    $traductValue = $translator->getTranslate($value, $this->getParameter('DEEPL_API'));


                    // on ajoute la valeur traduite dans le tableau de résultat
                    $result['translate-' . $count][$element] = $traductValue;

                    // si l'élément est le h1
                    if ($element === "h1") {
                        //on ajoute au tableau qui contiendra les éléments du fichier texte la valeur en français suivie de la valeur en anglais
                        $txtFile['translate-' . $count][$element] = $value;
                        $txtFile['translate-' . $count]['traduction'] = $traductValue;
                    }
                    // puis on ajoute la traduction du h1 dans le tableau de resultat
                    $result['translate-' . $count][$element] = $traductValue;
                } else {
                    //si la date n'est pas vide
                    if ($value != "") {
                        //on l'ajoute au tableau qui contiendra les éléments du fichier text
                        $txtFile['translate-' . $count][$element] = $value;
                    } else {
                        // sinon on ajoute la valeur suivante
                        $txtFile['translate-' . $count][$element] = "pas de date renseignée ou pas publié";
                    }
                }
            }
            $count++;
        }

        //on créée les fichiers de traduction et la liste des textes
        $trad = $file->make_File($nom_blog, 'traductions', '.json', $result);
        $list = $file->make_File($nom_blog, 'listes', '.txt', $txtFile);

        // on récupère la date actuelle et ajoute en bdd
        $current_date = new DateTime();
        $date = new Date();
        $date->setDate($current_date);
        $dateRepository->save($date, true);

        //on ajoute les fichiers en bdd
        $this->add_file_bdd($trad, $date, $site, 'traduction', $fichierRepository, $outilsRepository, "spinnerman");
        $this->add_file_bdd($list, $date, $site, 'liste', $fichierRepository, $outilsRepository, "spinnerman");

        return new Response(content: json_encode($result));

    }

    return new Response(content: json_encode(['WRONG USER' => 'WRONG USER MESSAGE']));
    }


    #[Route('/spinned', name: 'app_spin', methods: ['POST'])]
    public function spin(Request $request): Response
    {
        $user = $this->getUser();
        if($user){
        //on récupère le contenu du xml et on converti le json en objet itérable
        $entityBody = file_get_contents('php://input');
        $body = json_decode($entityBody);
        $translator = new TranslatorWa(); //on instancie un object TranslatorWa
        $result = []; //tableau des resultats qui sera retourné

        foreach ($body as $element => $value) {
            if ($element != "url") {
                // $result[$element] = "encore du blabla spinnée";
                $result[$element] = $translator->getSpinned($value, $this->getParameter('WORDAI_API'), $this->getParameter('MAIL'))->text;
            } else {
                $result[$element] = $value;
            }
        }
        return new Response(content: json_encode($result));
    }

    return new Response(content: json_encode(['WRONG USER' => 'WRONG USER MESSAGE']));
    }


    #[Route('/make_csv_file/{nom_blog}', name: 'app_mkf', methods: ['POST'])]
    public function make_File(string $nom_blog, Request $request, DateRepository $dateRepository, SiteRepository $siteRepository, OutilsRepository $outilsRepository, FichierRepository $fichierRepository): Response
    {
        $user = $this->getUser();
        if($user){
        $lastDate = $dateRepository->findLastEntry();
        $site = $siteRepository->findBy(['nom' => $nom_blog]);
        $entityBody = file_get_contents('php://input');
        $body = json_decode($entityBody, true);
        $loader = new File;
        $nom_csv = $loader->save_csv($body, $nom_blog);

        $this->add_file_bdd($nom_csv . '.csv', $lastDate[0], $site[0], 'spin', $fichierRepository, $outilsRepository, "spinnerman");


        return new Response(content: json_encode($nom_csv));
    }

    return new Response(content: json_encode(['WRONG USER' => 'WRONG USER MESSAGE']));
    }

    #[Route('/get_existing_files/{nom_blog}', name: 'app_get_existing_files', methods: ['POST'])]
    public function get_existing_files(string $nom_blog, FichierRepository $fichierRepository, SiteRepository $siteRepository): Response
    {     $user = $this->getUser();
        if($user){
        $site = $siteRepository->findby(['nom'=> $nom_blog]);
        if(count($site) > 0){
            $id_blog = $site[0]->getId();
            $result_request= $fichierRepository->findBy(['site'=> $id_blog, 'type'=> 'traduction']);
            foreach($result_request as $file){
                $list_file = $fichierRepository->findby(['site'=> $id_blog,'date'=> $file->getDate(),'type'=> 'liste']);
                $result[$file->getNomPourUtilisateur()."-".$file->getDate()] = [$file->getNomBdd() , count($list_file)  > 0 ? $list_file[0]->getNomBdd() : "pas de liste"];
            }
            return new Response(content: json_encode($result));
        }
        return new Response(content: json_encode(["aucun fichier trouvé" => "aucun fichier trouvé"]));
    }
    return new Response(content: json_encode(['WRONG USER' => 'WRONG USER MESSAGE']));   
    }

    #[Route('/get_trad_content/{fichier_trad}', name: 'app_get_trad_content', methods: ['POST'])]
    public function get_trad_content(string $fichier_trad): Response
    {
        $user = $this->getUser();
        if($user){
        $json = __DIR__.'/../../public/assets/uploads/traductions/'.$fichier_trad;
        $data = file_get_contents($json); 
        $obj = json_decode($data); 
        return new Response(content: json_encode($obj));
        
    }

    return new Response(content: json_encode(['WRONG USER' => 'WRONG USER MESSAGE']));
    }



    /**
     * ajoute les noms des fichiers et leurs types en bdd
     *
     * @param string $fileName
     * @param Date $date
     * @param Site $site
     * @param string $type
     * @param FichierRepository $fichierRepository
     * @param OutilsRepository $outilsRepository
     * @return void
     */
    private function add_file_bdd(string $fileName, Date $date, Site $site, string $type, FichierRepository $fichierRepository, OutilsRepository $outilsRepository, string $nomOutils)
    {
        $outil = $outilsRepository->findBy(['nom' => $nomOutils]);
        $file = new Fichier();
        $file->setNomBdd($fileName);
        $file->setNomPourUtilisateur($this->formatUserFileName($fileName, $type));
        $file->setDate($date);
        $file->setSite($site);
        $file->setOutils($outil[0]);
        $file->setType($type);
        $fichierRepository->save($file, true);
    }

    /**
     * formatte le nom du fichier pour l'entrée nomPourUtilisateur de la bdd
     *
     * @param string $name
     * @return string
     */
    private function formatUserFileName(string $name, string $type):string
    {
        return $type.'-'.explode('.', $name)[0];
    }


    /**
     * Récupère le slug d'une URL
     *
     * @param string $url
     * @return string
     */
    private function urlToSlug(string $url): string
    {
        // on transforme l'url en tableau
        $array_url = explode('/', $url);
        //on retire les éléments vides du tableau
        $urls = array_filter($array_url);
        //on renvoie le dernier élément du tableau qui correspond au slug
        return end($urls);
    }

    /**
     * Vérifie si le nom du blog existe ou non en bdd
     *
     * @param string $name
     * @param SiteRepository $siteRepository
     * @return boolean
     */
    public static function is_new(string $name, SiteRepository $siteRepository)
    {
        $sites = $siteRepository->findAll();
        foreach ($sites as $site) {
            if ($site->getNom() === $name) {
                return false;
            }
        }
        return true;
    }


    
    #[Route('/make_zip/{nom_blog}', name: 'app_mkzip', methods: ['POST'])]
    public function make_zip(string $nom_blog, DateRepository $dateRepository, SiteRepository $siteRepository, FichierRepository $fichierRepository, OutilsRepository $outilsRepository): Response
    {
        $site = $siteRepository->findOneBy(['nom' => $nom_blog]);
        $user = $this->getUser();
        if($user){
        $entityBody = file_get_contents('php://input');
        $body = json_decode($entityBody, true);
        $zip = new File();
        $response = $zip->make_zip($body, $nom_blog);
      
        $time= time();
        $current_date = new DateTime();
        $date = new Date();
        $date->setDate($current_date);
        $dateRepository->save($date, true);

        $this->add_file_bdd("images-".$nom_blog.$time, $date, $site, "zip", $fichierRepository, $outilsRepository, "Extrac-thor" );
        
        return new Response(content: json_encode(['zipFile'=> $response]));
    }

    return new Response(content: json_encode(['WRONG USER' => 'WRONG USER MESSAGE']));
    }



    #[Route('/dl_file/{nom_blog}', name: 'app_dlf', methods: ['POST'])]
    public function dl_file(string $nom_blog): Response
    {
            $user = $this->getUser();
            if($user){

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

            return new Response(content: json_encode(['WRONG USER' => 'WRONG USER MESSAGE']));

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
