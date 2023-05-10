window.onload = () => {
  // On récupère tous les boutons d'ouverture de modale
  const modalButtons = document.querySelectorAll("[data-toggle=modal]");

  for (let button of modalButtons) {
    button.addEventListener("click", function (e) {
      //data//
      let nom = document.querySelector('.nom-'+button.dataset.updid).textContent;
      let prenom = document.querySelector('.prenom-'+button.dataset.updid).textContent;
      let email = document.querySelector('.email-'+button.dataset.updid).textContent;
      let roles = document.querySelector('.roles-'+button.dataset.updid).textContent.trim();

      document.getElementById('users_update_form_nom').value = nom;
      document.getElementById('users_update_form_prenom').value = prenom;
      document.getElementById('users_update_form_email').value = email;
      document.getElementById('users_update_form_password').value = "";
      document.getElementById('users_update_form_roles').value = roles == "Collaborateur" ? "ROLE_USER" : "ROLE_ADMIN";
      document.getElementById('users_update_form_id').value = button.dataset.updid;
      
      console.log(roles)
      ///affichage///
      e.preventDefault();
      let target = this.dataset.target;
      let modal = document.querySelector(target);
      modal.classList.add("show");

      // On récupère les boutons de fermeture
      const modalClose = modal.querySelectorAll("[data-dismiss=dialog]");

      for (let close of modalClose) {
        close.addEventListener("click", () => {
          modal.classList.remove("show");
        });
      }

      // On gère la fermeture lors du clic sur la zone grise
      modal.addEventListener("click", function () {
        this.classList.remove("show");
      });
      // On évite la propagation du clic d'un enfant à son parent
      modal.children[0].addEventListener("click", function (e) {
        e.stopPropagation();
      });
    });
  }
};
