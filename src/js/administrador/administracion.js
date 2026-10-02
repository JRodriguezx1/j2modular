(function(){
    
    ///////////////////// habilita/deshabiita inputs de los dias de la malla de turnos con el checkbox/////////////
    const dias = document.querySelectorAll(".dia input[type=checkbox]");
    dias.forEach(dia => {
        dia.addEventListener('change', function(e){
            if(this.checked){
                for(let i=0; i<4; i++)
                e.target.parentElement.nextElementSibling.children[i].disabled = false;
            }else{ 
                for(let i=0; i<4; i++)
                e.target.parentElement.nextElementSibling.children[i].disabled = true;
            }
        });
    });


    ////////////////////// carga de horas en los select de la malla////////////////////////
    const entradas = document.querySelectorAll('#entrada');
    entradas.forEach(entrada =>{
        entrada.addEventListener('change', (e)=>{
            let nextselect = e.target.nextElementSibling; //select siguiente
            puthoras(nextselect, e);
        });
    });

    function puthoras(nextselect, e){
        while(nextselect.firstChild)nextselect.removeChild(nextselect.firstChild);
        let subhora = e.target.value.split(':');
        let hora = parseInt(subhora[0]+subhora[1]);

        for(let i = 0; hora<2300; i++){
            let option = document.createElement('OPTION');
            hora+=30;
            if(hora%100 == 60)hora+=40;
            option.value = hora;
            subhora[0]= parseInt(hora/100);
            subhora[1] = hora%100;
            if(subhora[1] === 0)subhora[1] = "00";
            option.textContent = subhora[0]+':'+subhora[1];
            nextselect.appendChild(option);
        }
        nextselect.addEventListener('change', (e)=>{
            let nextselect = e.target.nextElementSibling; //select siguiente
            if(nextselect)puthoras(nextselect, e);
        });
    }

    

})();