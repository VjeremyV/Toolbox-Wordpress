<?php
namespace App\Classes;

use DeepL\Translator;

/**
 * Pour la traduction et le spin des textes
 */
class TranslatorWa {
    /**
     * Traduit un text via l'appel à l'api Deepl
     *
     * @param [string] $text le texte à traduire
     * @return string
     */
    public function getTranslate(string $text, string $API_Key):string{
        //on instancie un objet DeepL\Translator
        $translator = new Translator($API_Key);
        //on fait la requête de traduction
        $result =  $translator->translateText($text, null, 'EN-US', ["tag_handling" => "html"]);
        //on retourne uniquement le texte
        return $result->text;
    }

    /**
     * Spinne un texte donnée via la connexion avec worldai
     *
     * @param [string] $text
     * @return mixed
     */
    public function getSpinned(string $text, string $API_key, string $mail):mixed{

        //informations endpoint API
        $url = 'https://wai.wordai.com/api/rewrite';

        // on instancie un objet qui requête les Api
        $caller = new Request_Api();

        //on renvoie le resultat du call API
        return json_decode($caller->CallAPI('POST', $url, [
            'email'=> $mail,
            'key'=> $API_key,
            'return_rewrites' => true,
            'rewrite_num'=> 1,
            'uniqueness' => 3,
            'input'=> $text
        ]));
    }



}


