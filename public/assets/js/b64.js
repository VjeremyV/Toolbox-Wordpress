(() => {
  let URL = document.getElementById("URL");
  let URLs = document.getElementById("URLs");
  let btn = document.getElementById("transformBtn");
  let tbody = document.getElementById('tbody');
  let b64OneUrl= document.getElementById("B64");
    
  ////////////////////////////////////////////////////////////Fonctions////////////////////////////////////////////
  /**
   * transofrme une URL en base 64
   * @param {string} url 
   * @returns 
   */
  let inputEncode = function (url) {
    let urlToBeEncoded = url;
    let urlEncoded =
      urlToBeEncoded && window.btoa(encodeURIComponent(urlToBeEncoded.trim()));
      return urlEncoded
  };
  
  /**
   * copie le texte
   * @param {} copyText 
   */
  function CopyText(copyText) {
    if(copyText.textContent){
      navigator.clipboard.writeText(copyText.textContent);
        alert("Copié: " + copyText.textContent);
    } else {
      navigator.clipboard.writeText(copyText.value);
      alert("Copié: " + copyText.value);
    }
  }
  
  
  
  
  
  ///////////////////////////////////////////////////////////////////Evenements////////////////////////////////////////////////////
  
  /**
   * au click sur le bouton transformer
   */
  btn.addEventListener('click', () => {
    let urlsArray= URLs.value.split('\n');//je split les url avec le retour à la ligne
    let b64Array = [];
    urlsArray.forEach(url => {//pour chaque url
      b64Array.push({uurl: url, b64: `&lt;span class='qcd' data-qcd='${inputEncode(url.trim())}'&gt;Ancre à changer&lt;/span&gt;`});//je rajotue une entrée au tableau correspodant au span avec le b64
    });
    
    //on injecte dans la balise tbody pour chaque iteration du tableau 
    tbody.innerHTML = b64Array.map((b64, i) => `
    <tr>
    <td class='col-2'>${i+1}</td>
    <td class='col-5'>${b64.uurl}</td>
    <td class='copyText col-5'>${b64.b64}</td>
  </tr>`).join('')
  
  //on change les valeurs dans le textarea pour ajouter les id
    URLs.value = urlsArray.map((url, i) => `${i+1} - ${url}
  `).join('');
  
  let spans = document.querySelectorAll(".copyText");
  
  spans.forEach((element) => {
  let span = element;
    element.addEventListener('click', () => {
      CopyText(span);
    })
  })
  })
  
  URL.addEventListener("input", (e) => {
    b64OneUrl.value = `<span class='qcd' data-qcd='${inputEncode(e.target.value)}'>Ancre à changer</span>`;
  });
  
  b64OneUrl.addEventListener('click', () => {
    CopyText(b64OneUrl);
  })
})()
