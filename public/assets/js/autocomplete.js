export { searchStates, DisplayAutocompletion }
import { get_existing_traduction_files } from './spinnermanFunctions.js'
  /**
   * compare la recherche avc les noms de sites en bdd pour faire l'auto-completion
   * @param {string} search 
   */
  async function searchStates(searchTag, htmlTag, options) {
    fetch('/historique/getSites/', {...options})
    .then((res)=>{
      return res.json();})
    .then((data) => {
      //on filtre les noms de sites pour ne garder que les resultats qui commencent par la chaine de caractères demandée

      let matches = data.filter(state => {
        const regex = new RegExp(`^${searchTag.value}`, 'gi');
        return state.match(regex);
      })
      //si la recherche est vide on vide également le tableau de reponses, sinon il nous renvoie le tableau avec tous les noms car ils peuvent tous correspondre
      if(searchTag.value.length === 0){
        matches = "";
        htmlTag.innerHTML = "";
        htmlTag.style.border = `none`

      }
      
      DisplayAutocompletion(matches, htmlTag, searchTag, options)
    })
  }

  function DisplayAutocompletion(matches, htmlTag, searchTag , options){
    if(matches.length > 0){
      const html = matches.map((result) => 
      `
      <div class="resultAutocomplete"><span>${result}</span></div>
      `
      ).join('');

      htmlTag.innerHTML = html;
      htmlTag.style.border = `1px solid black`
      htmlTag.style.bottom = -htmlTag.offsetHeight+"px"

      //selon l'url détéctée on active ou non la recherche des fichiers de traduction en bdd ou non
      let slug = window.location.href.split('/').slice(-1);
      if(slug[0].trim() === 'spinnerman' || slug[0].trim() === 'extracteur-images'){
        clikcEvent(searchTag, htmlTag)
      } else if(slug[0].trim() ==="existing_blog"){
        clikcEvent(searchTag, htmlTag, options)
      }
    }

    function clikcEvent(searchTag, htmlTag, options = false){
      let resultAutocomplete = document.querySelectorAll('.resultAutocomplete');
      resultAutocomplete.forEach((Element)=> {
        Element.addEventListener('click', (e)=> {
          searchTag.value = e.target.textContent;
          if(options) {
            get_existing_traduction_files(e.target.textContent.trim(), options)
          }
          htmlTag.innerHTML = "";
          htmlTag.style.border = `none`
        })
      })
    }
  }