import {
  spinData,
  get_existing_traduction_files,
  clean_data,
  end_process,
} from "./spinnermanFunctions.js";

import { searchStates } from "./autocomplete.js";

(() => {
  /***************************************************************DEBUT VARIABLES ****************************************************/
  let options = {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
  };
  let nomBlog = document.getElementById("form_nom_blogExisting");
  let matchList = document.getElementById("matchList");
  let form = document.getElementById("entryFileExisting");
  let existingFiles = document.getElementById("existingFiles");
  let spiderContainer = document.getElementById("spider_container"); //le contenainer des animations de l'araignée et des resultats de fin de traitement
  let makeFileProgress = document.querySelector(".makeFileProgress"); //le container d'état de progression de création du ficheir de sortie
  let spinProgress = document.querySelector(".spinProgress"); // le container d'état de progression du spin
  let optionTxtDataset;
  /***************************************************************FIN VARIABLES ****************************************************/

  nomBlog.addEventListener("input", (e) => {
    searchStates(e.target, matchList, options, true);
    if (e.target.value != "") {
      get_existing_traduction_files(e.target.value, options);
    }
  });

  existingFiles.addEventListener("change", (e) => {
    optionTxtDataset = (existingFiles.options[e.target.selectedIndex].dataset.txt);
  });


  form.addEventListener("submit", (e) => {
    e.preventDefault();
    spiderContainer.classList.add("web");
    spinProgress.innerHTML = `<span class="font-weight-bold m-3">Spin en cours ...</span>`;

    fetch("/get_trad_content/" + existingFiles.value, { ...options })
      .then((res) => {
        return res.json();
      })
      .then((data) => {
        spinData(data, options, spinProgress)
          .then((res) => {
            let clean_spinned_array = clean_data(res);
            spinProgress.innerHTML = `<span class="font-weight-bold alert text-success m-3"><i class="fa-solid fa-check"></i> Spin Terminé</span>`;
            makeFileProgress.innerHTML = `<span class="font-weight-bold m-3">Création du fichier d'export csv ...</span>`;
            end_process( makeFileProgress, spiderContainer, clean_spinned_array, nomBlog, options, optionTxtDataset);
          })
          .catch((err) => {
            console.log("Spin fail: ", err);
            spinProgress.innerHTML = `<span class="font-weight-bold alert text-danger m-3"><i class="fa-solid fa-xmark"></i> Un problème est survenu lors du spin des textes</span>`;
          });
      });
  });
})();
