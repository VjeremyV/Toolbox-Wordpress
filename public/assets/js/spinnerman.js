import { runSpinnerman } from "./spinnermanFunctions.js";
import { searchStates } from "./autocomplete.js";

(() => {
  //////////////////////////////////////////VARIABLES///////////////////////////////////////////////////////////
  let nomBlog = document.getElementById("form_nom_blog"); //le contenu de l'input text du nom du blog
  let matchList = document.getElementById("matchList");  // le conteneur des resultats de l'autocomplete
  // l'objet option qui nous servira de base pour les appels fetch
  let options = {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
  };
  ///////////////////////////////////////////////////FIN VARIABLES - DEBUT AUTO-COMPLETETION////////////////////////////////////////////////////////////

  nomBlog.addEventListener("input", (e) => {
    searchStates(e.target, matchList, options);
  });

  ///////////////////////////////////////////////////FIN AUTO-COMPLETETION - DEBUT TRAITEMENT DES DONNEES////////////////////////////////////////////////////////////

  // Au chargement de la page si le xml est envoyé on joue le script de traitement sinon rien ne se passe
  if (xml) {
   // on définit les options pour l'API de traduction'
   
//le contenainer des animations de l'araignée et des resultats de fin de traitement
    let spiderContainer = document.getElementById("spider_container"); 

    // le container d'état de progression de la traduction
    let translateProgress = document.querySelector(".translateProgress"); 

    // le container d'état de progression du spin
    let spinProgress = document.querySelector(".spinProgress"); 

    //le container d'état de progression de création du fichier de sortie
    let makeFileProgress = document.querySelector(".makeFileProgress"); 

    runSpinnerman(spiderContainer, translateProgress, spinProgress, makeFileProgress, nomBlog, options);
  }
})();
