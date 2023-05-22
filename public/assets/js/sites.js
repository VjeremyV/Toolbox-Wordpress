(()=> {
    let selectAllBtn = document.getElementById('selectAll');
    let selectBtns = document.querySelectorAll('.selectCheckbox');
    let multiSppBtn = document.getElementById('multiSppBtn');
    let options = {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
      };
    let idSite = document.querySelectorAll('.selectCheckbox');
    let responseMessage = document.getElementById('responseMessage');
    let responseMessageBad = document.getElementById('responseMessageBad');
    ///////////////////////////////evenement SelectAllcheckbox////////////////////////////////////
    selectAllBtn.addEventListener('change', () => {
        selectBtns.forEach((btn)=> {
            btn.checked = selectAllBtn.checked;
            if(checkIfmanySupp()){
                multiSppBtn.style.display = 'block';
            } else {
                multiSppBtn.style.display = 'none';

            }
        })
    })
////////////////////////////////evenement checkbox individuel///////////////////////////////////////
    selectBtns.forEach((btn)=> {
        btn.addEventListener('change', () => {
            if(checkIfmanySupp()){
                multiSppBtn.style.display = 'block';
            }   else {
                multiSppBtn.style.display = 'none';

            }     
        })
    })

    multiSppBtn.addEventListener('click', async () => {
        let sites = [];
        idSite.forEach((site) => {
            if(site.checked){
                sites.push(site.dataset.id);
            }
        })
        let response = await fetch('/sites/multisupp/', {
            ...options,
            body: JSON.stringify(sites),
        });
        let data = await response.json();

        if(data.good){
            console.log(data)
            responseMessage.style.display = "block";
            responseMessage.innerHTML = data.good;
        } else {
            responseMessageBad.style.display = "block";
            responseMessageBad.innerHTML = data.err;
        }

        setTimeout(() => {
            window.location.replace("/sites");
        }, 2000)
    })


    //////////////////////////////////////////////fonctions////////////////////////////////////////////////////////
    function checkIfmanySupp(){
        let count = 0;
        selectBtns.forEach((btn) => {
            if(btn.checked){
                count++;
            }
        })
        if(count != 0){
            return true;
        }
        return false;
    }
})()