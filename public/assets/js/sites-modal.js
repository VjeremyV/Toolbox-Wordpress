(() => {
  // On récupère tous les boutons d'ouverture de modale
  window.onload = () => {
    const modalButtons = document.querySelectorAll("[data-toggle=modal]");
    const iframe = document.getElementById('changeUrlIframe');

    for (let button of modalButtons) {
      button.addEventListener("click", function (e) {
        // On empêche la navigation
        e.preventDefault();
        // On récupère le data-target
        let target = this.dataset.target;
        let siteId = this.dataset.id;
        console.log(siteId)
        // On récupère la bonne modale
        let modal = document.querySelector(target);
        // On affiche la modale
        iframe.setAttribute('src', '/changeUrl/'+siteId)
        modal.classList.add("show");

        // On récupère les boutons de fermeture
        const modalClose = modal.querySelectorAll("[data-dismiss=dialog]");

        for (let close of modalClose) {
          close.addEventListener("click", () => {
            modal.classList.remove("show");
            setTimeout(() => {
              window.location.replace("/sites");
          }, 2000)
          });
        }

        // On gère la fermeture lors du clic sur la zone grise
        modal.addEventListener("click", function () {
          this.classList.remove("show");
          setTimeout(() => {
            window.location.replace("/sites");
        }, 2000)
        });
        // On évite la propagation du clic d'un enfant à son parent
        modal.children[0].addEventListener("click", function (e) {
          e.stopPropagation();
        });
      });
    }
  };
})();
