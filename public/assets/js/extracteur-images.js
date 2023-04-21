import { downloadFiles } from './extracteur-images.Functions.js'
import{
    searchStates 
  } from './autocomplete.js'
(() => {
  /**************************************************************DEBUT VARIABLES ******************************************/
  let options = {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
  };
  let siteName = document.getElementById("form_nom_blog");
  let nomBlog = document.getElementById("form_nom_blog"); //le contenu de l'input text du nom du blog
  let matchList = document.getElementById("matchList");
  let responseContainer = document.getElementById('response_container');
  /**************************************************************FIN VARIABLES ******************************************/

  nomBlog.addEventListener("input", (e) => {
    searchStates(e.target, matchList, options);
  });

  if (xml) {
    downloadFiles(xml, siteName.value, options, responseContainer);
  }
 
})();
