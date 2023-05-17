(()=> {
    let selectAllBtn = document.getElementById('selectAll');
    let selectBtns = document.querySelectorAll('.selectCheckbox');
    let multiSppBtn = document.getElementById('multiSppBtn');

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

    selectBtns.forEach((btn)=> {
        btn.addEventListener('change', () => {
            if(checkIfmanySupp()){
                multiSppBtn.style.display = 'block';
            }   else {
                multiSppBtn.style.display = 'none';

            }     
        })
    })

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