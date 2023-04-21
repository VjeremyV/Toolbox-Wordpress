import {
  retrieveData, 
  clean_data,
  end_process
} from "./spinnermanFunctions.js";

import{
  searchStates 
} from './autocomplete.js'

(() => {
  //////////////////////////////////////////VARIABLES///////////////////////////////////////////////////////////

  // on définit les options pour l'API de traduction'
  let options = {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
  };

  let spiderContainer = document.getElementById("spider_container"); //le contenainer des animations de l'araignée et des resultats de fin de traitement
  let translateProgress = document.querySelector(".translateProgress"); // le container d'état de progression de la traduction
  let spinProgress = document.querySelector(".spinProgress"); // le container d'état de progression du spin
  let makeFileProgress = document.querySelector(".makeFileProgress"); //le container d'état de progression de création du ficheir de sortie
  let nomBlog = document.getElementById("form_nom_blog"); //le contenu de l'input text du nom du blog
  let matchList = document.getElementById("matchList");


  ///////////////////////////////////////////////////FIN VARIABLES - DEBUT AUTO-COMPLETETION////////////////////////////////////////////////////////////

  nomBlog.addEventListener("input", (e) => {
    searchStates(e.target, matchList, options);
  });

  
  ///////////////////////////////////////////////////FIN AUTO-COMPLETETION - DEBUT TRAITEMENT DES DONNEES////////////////////////////////////////////////////////////

  // Au chargement de la page si le xml est envoyé on joue le script de traitement sinon rien ne se passe
  if (xml) {
    //On ajoute la class web au spidercontainer pour que les règles css fassent apparaitre l'animation de l'araignée
    spiderContainer.classList.add("web");
    translateProgress.innerHTML = `<span class="font-weight-bold m-3">Traduction en cours ...</span>`;

    fetch("/translate/" + nomBlog.value, {
      ...options,
      body: JSON.stringify(xml),
    })
      .then((res) => {
        return res.json();
      })
      .then((data) => {
        translateProgress.innerHTML = `<span class="font-weight-bold alert text-success m-3"><i class="fa-solid fa-check"></i> Traduction Terminée</span>`;
        spinProgress.innerHTML = `<span class="font-weight-bold m-3">Spin en cours ...</span>`;

        retrieveData(data, options, spinProgress)
          .then((res) => {
            let clean_spinned_array = clean_data(res);
            spinProgress.innerHTML = `<span class="font-weight-bold alert text-success m-3"><i class="fa-solid fa-check"></i> Spin Terminé</span>`;
            makeFileProgress.innerHTML = `<span class="font-weight-bold m-3">Création du fichier d'export csv ...</span>`;

            end_process(makeFileProgress, spiderContainer, clean_spinned_array, nomBlog, options)
          
          })
          .catch((err) => {
            console.log("Spin fail: ", err);
            spinProgress.innerHTML = `<span class="font-weight-bold alert text-danger m-3"><i class="fa-solid fa-xmark"></i> Un problème est survenu lors du spin des textes</span>`;
          });
      })
      .catch((err) => {
        console.log("Translate fail: ", err);
        translateProgress.innerHTML = `<span class="font-weight-bold alert text-danger m-3"><i class="fa-solid fa-xmark"></i> Un problème est survenu lors de la traduction des textes</span>`;
      });
  }
})();
