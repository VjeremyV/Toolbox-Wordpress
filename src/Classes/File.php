<?php

namespace App\Classes;

use App\Repository\FichierRepository;
use ZipArchive;

class File
{
    /**
     * Undocumented function
     *
     * @param mixed $xml
     * @return array
     */
    public function display_xml(mixed $xml): array
    {
        $items = [];
        $rss_load = simplexml_load_file($xml);
        foreach ($rss_load->channel->item as $item) {

            foreach ($item->postmeta as $postmeta) {
                // $titre[]= $postmeta->meta_value->__toString();
                if (trim($postmeta->meta_key->__toString()) == "_yoast_wpseo_title") {
                    $titre =  $postmeta->meta_value->__toString();
                    break;
                } elseif (trim($postmeta->meta_key->__toString() == "_aioseop_title")) {
                    $titre =  $postmeta->meta_value->__toString();
                }
            }

            //on définit les contenus
            // $titre = self::what_title($item);
            $contenu = self::check_xml_tags($item, 'encoded', 'content:');
            $title = self::check_xml_tags($item, 'title', 'wp:');
            $link = self::check_xml_tags($item, 'link', 'wp:');
            $date = self::check_xml_tags($item, 'pubDate', ':wp');
            $titre = self::what_title($item);

            $items[] = [
                'date' => trim($date[0]->__toString()),
                'url' => trim($link[0]->__toString()),
                'title' => trim($titre),
                'h1' => trim($title[0]->__toString()),
                'contenu' => trim($contenu[0]->__toString())
            ];
        }
        return $items;
    }


    /**
     * créée le fichier csv des menus et sauvegarde les données
     *
     * @param [array] $tab
     * @return string
     */
    public function save_csv(array $tab, string $nom_blog): string
    {

        $date = date('d-m-Y');
        $nom_csv = $nom_blog . $date;
        $csv = __DIR__ . '/../../public/assets/uploads/spins/' . $nom_csv . '.csv';
        $fp = fopen($csv, 'w');
        fputcsv($fp, ['H1', 'TITRE SEO', 'SLUG', 'TEXTE'], "*", "#");

        foreach ($tab as $line) {
            $newline = [];
            foreach ($line as $value) {
                $newline[] = self::suppr_line_break($value);
            }

            fputcsv($fp, $newline, "*", "#");
        }
        // fwrite($fp, "coucou"); va ecrire dans notre fichier csv $fp la string "coucou"
        fclose($fp); //ferme le fichier

        //Remplacement des '*' en ''
        $replace = str_replace("#", "", file_get_contents($csv));
        file_put_contents($csv, $replace);

        return $nom_csv;
    }

    /**
     * Undocumented function
     *
     * @param string $nom_blog
     * @param string $dir
     * @param string $extension
     * @param array $contenu
     * @return string
     */
    public function make_file(string $nom_blog, string $dir, string $extension, array $contenu): string
    {
        $date = date('d-m-Y');
        $nom_fichier = $nom_blog . $date . $extension;
        $fichier = __DIR__ . '/../../public/assets/uploads/' . $dir . '/' . $nom_fichier;
        $fp = fopen($fichier, 'w');

        if ($extension === ".json") {
            fwrite($fp, json_encode($contenu));
        } else {
            foreach ($contenu as $element) {
                fwrite($fp, implode('     /     ', $element));
                fwrite($fp, "\n");
            }
        }

        fclose($fp);
        return $nom_fichier;
    }
    /**
     * Undocumented function
     *
     * @param [type] $chaine
     * @return void
     */
    private function suppr_line_break($chaine)
    {
        return preg_replace("# {2,}#", " ", preg_replace("#(\r\n|\n\r|\n|\r)#", " ", $chaine));
    }

    /**
     * vérifie le tag xml et renvoie soit le noeu ou le nom du tag
     *
     * @param mixed $item l'item xml contenant
     * @param string $tag le tag à vérifier
     * @param string $prefixe le préfixe à vérifier
     * @param boolean $tag_only si on a simplement besoin du nom du noeud
     * @return mixed
     */
    private function check_xml_tags(mixed $item, string $tag, string $prefixe, $tag_only = false): mixed
    {
        if (!$tag_only) {
            if ($item->xpath($tag)) {
                return $item->xpath($tag);
            }
            return $item->xpath($prefixe . $tag);
        }
        if ($item->xpath($tag)) {
            return $tag;
        }
        return $prefixe . $tag;
    }

    /**
     * renvoie le bon h1 en fonction de la version du wordpress qui a produit le xml d'export
     *
     * @param mixed $item
     * @return string
     */
    private function what_title(mixed $item): string
    {
        // On définit nos noeuds xml
        $postmeta_tag = self::check_xml_tags($item, 'postmeta', 'wp:', true);
        $meta_key_tag = self::check_xml_tags($item, 'meta_key', 'wp:', true);
        $meta_value_tag = self::check_xml_tags($item, 'meta_value', 'wp:', true);

        //On définit une valeur par défaut pour notre titre
        $titre = "vide";


        if (str_starts_with($postmeta_tag, 'wp:')) {
            foreach ($item->xpath('wp:postmeta') as $postmeta) {
                if (trim($postmeta->xpath($meta_key_tag)[0]->__toString()) == "_yoast_wpseo_title") {
                    $titre =  $postmeta->xpath($meta_value_tag)[0]->__toString();
                    break;
                } elseif (trim($postmeta->xpath($meta_key_tag)[0]->__toString() == "_aioseop_title")) {
                    $titre =  $postmeta->xpath($meta_value_tag)[0]->__toString();
                }
            }
        } else {
            foreach ($item->postmeta as $postmeta) {
                if (trim($postmeta->meta_key->__toString()) == "_yoast_wpseo_title") {
                    $titre =  $postmeta->meta_value->__toString();
                    break;
                } elseif (trim($postmeta->meta_key->__toString() == "_aioseop_title")) {
                    $titre =  $postmeta->meta_value->__toString();
                }
            }
        }
        return $titre;
    }

    public function make_zip(array $urls, string $site_name, string $zip_FileName)
    {
        $zip = new ZipArchive();
        if ($zip->open(__DIR__ . "/../../public/assets/uploads/zip_files/" .$zip_FileName .".zip", ZipArchive::CREATE) === true) {
            $path = __DIR__ . "/../../public/assets/uploads/zip_imgs/" . $site_name  ;
            foreach ($urls as $data) {
                foreach ($data as $elements) {
                    $file_name = array_pop($elements);

                    $directory = "";
                    foreach ($elements as $dir) {
                        $directory .= $dir . '/';
                    }
                    //     // // Ajout d’un fichier.
                    $fp = $path. "/" . $directory . $file_name;

                    $zip->addFile($fp, $directory . $file_name);
                }
            }
            // Et on referme l'archive.
            $zip->close();
            //on supprime les fichiers temporaires
            $this->rrmdir($path);
            return "/assets/uploads/zip_files/".$zip_FileName. ".zip";
        } else {
            var_dump("Impossible d'ouvrir le .zip<br/>");
            die();
            // Traitement des erreurs avec un switch(), par exemple.
        }
    }

    public function download_file(string $url, array $directories, string $site_name, string $file_name)
    {
        $directory = "";
        $path = __DIR__ . "/../../public/assets/uploads/zip_imgs/" . $site_name;
        if (!file_exists($path)) {
            mkdir($path);
        }
        foreach ($directories as $dir) {
            $directory .= $dir . '/';
            $img_path = __DIR__ . "/../../public/assets/uploads/zip_imgs/" . $site_name . "/" . $directory;
            if (!file_exists($img_path)) {
                mkdir($img_path);
            }
        }
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        $fp = __DIR__ . "/../../public/assets/uploads/zip_imgs/" . $site_name . "/" . $directory . $file_name;
        file_put_contents($fp, $data);
        return true;
    }

    private function rrmdir($src) {
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $full = $src . '/' . $file;
                if ( is_dir($full) ) {
                    $this->rrmdir($full);
                }
                else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
    }
}
