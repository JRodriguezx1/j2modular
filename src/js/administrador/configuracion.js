(function(){
    if(document.querySelector('.configAdd')){
        let arraymediospago = [];
        const inputmediospago = document.querySelectorAll('.mediospago__mediopago input[type="checkbox"]');
        const btnbtnmediopago = document.querySelector('#btnmediopago');
        

        (async()=>{
            try {
                const url = "/admin/api/getmediospago";
                const respuesta = await fetch(url);
                const resultado = await respuesta.json();
                marcarmediospago(resultado);
            } catch (error) {
                console.log(error);
            }
        })();

        function marcarmediospago(resultado){
            inputmediospago.forEach(input =>{
                const r = resultado.find(mediopago=>mediopago.id == input.value); //retorna el obj que conside
                if(r.estado === '1'){ //r es el obj retornado
                    input.checked = true;
                    arraymediospago = [...arraymediospago, r.id];
                } 
            });
        }

        inputmediospago.forEach(input =>{
            input.addEventListener('change', (e)=>{
                if(e.target.checked){
                    arraymediospago = [...arraymediospago, e.target.value];
                }else{
                    arraymediospago = arraymediospago.filter(Element=>Element!=e.target.value);
                }
            });     
        });

        btnbtnmediopago.addEventListener('click', ()=>{
            (async ()=>{
                const datos = new FormData();
                datos.append('ids', arraymediospago);
                try {
                    const url = "/admin/api/setmediospago";
                    const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                    const resultado = await respuesta.json();  
                    if(resultado){
                        msjalertToast('success', '¡Éxito!', 'Medios de pago actualizados');
                    }else{
                        msjalertToast('error', '¡Error!', "Se produjo un error intentalo nuevamente");
                    }
                } catch (error) {
                    console.log(error);
                }
            })();
        });
        ////////************************fin medios de pago*************************//////////

        const formcolores = document.querySelector('#formcolores');
        formcolores.addEventListener('submit', (e)=>{
            e.preventDefault();
            const color1 = document.querySelector('.coloresapp #principal').value;
            const color2 = document.querySelector('.coloresapp #secundario').value;
            (async ()=>{
                const datos = new FormData();
                datos.append('colorprincipal', color1);
                datos.append('colorsecundario', color2);
                try {
                    const url = "/admin/api/coloresapp";
                    const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                    const resultado = await respuesta.json(); 
                    if(resultado){
                        msjalertToast('success', '¡Éxito!', 'Colores establecidos correctamente');
                    }else{
                        msjalertToast('error', '¡Error!', 'Error al establecer los colores intentalonuevamnete');
                    }
                } catch (error) {
                    console.log(error);
                }
            })();
        });
        //////////*****************fin definir colores app******************//////////

        (async ()=>{
            try {
                const url = "/admin/api/gettimeservice"; //llamado a la API REST
                const respuesta = await fetch(url); 
                const gettimeservice = await respuesta.json(); //traer el tiempo de duracion del servicio
                marcartiemposervicio(gettimeservice);
            } catch (error) {
                console.log(error);
            }
        })();

        function marcartiemposervicio(gettimeservice){
            const selecttimeserv = document.querySelector('#selecttime');
            for(let i = 1; i<=selecttimeserv.options.length; i++){
                if(selecttimeserv.options[i].value == gettimeservice){
                    selecttimeserv.options[i].selected = true;
                    break;
                }
            }
        }

        const formtimeserv = document.querySelector('#formtimeserv');
        formtimeserv.addEventListener('submit', (e)=>{
            e.preventDefault();
            const selecttimeserv = document.querySelector('#selecttime');
            const tiemposervicio = selecttimeserv.options[selecttimeserv.options.selectedIndex].value;
            const datos = new FormData();
            datos.append('timeservice', tiemposervicio);
            (async()=>{
                try {
                    const url = "/admin/api/tiemposervicio";
                    const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                    const resultado = await respuesta.json(); 
                    console.log(resultado);
                    if(resultado){
                        msjalertToast('success', '¡Éxito!', 'Tiempo del servicio establecido');
                    }else{
                        msjalertToast('success', '¡Error!', 'Se produjo un error intentalo nuevamente');
                    }
                } catch (error) {
                    console.log(error);
                }
            })();
        });
        //////////*****************fin establecer tiempo de servicio******************//////////
    }  //fin pagina 5 = configuracion
})();