import { runSpinnerman } from "./spinnermanFunctions.js";
import { searchStates } from "./autocomplete.js";

(() => {
  //////////////////////////////////////////VARIABLES///////////////////////////////////////////////////////////
  let nomBlog = document.getElementById("form_nom_blog"); //le contenu de l'input text du nom du blog
  let matchList = document.getElementById("matchList");
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

    let spiderContainer = document.getElementById("spider_container"); //le contenainer des animations de l'araignée et des resultats de fin de traitement
    let translateProgress = document.querySelector(".translateProgress"); // le container d'état de progression de la traduction
    let spinProgress = document.querySelector(".spinProgress"); // le container d'état de progression du spin
    let makeFileProgress = document.querySelector(".makeFileProgress"); //le container d'état de progression de création du ficheir de sortie

    runSpinnerman(spiderContainer, translateProgress, spinProgress, makeFileProgress, nomBlog, options);
  }
})();
