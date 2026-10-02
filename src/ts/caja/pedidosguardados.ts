(():void=>{

  if(document.querySelector('.pedidosguardados')){

    let indiceFila=0, control=0, tablaPedidosGuardados:HTMLElement;
/*
    type cajasapi = {
        id:string,
        idtipoconsecutivo: string,
        nombre: string,
        negocio: string
      };
  
      let cajas:cajasapi[]=[], unacaja:cajasapi|undefined;
      (async ()=>{
        try {
            const url = "/admin/api/allcajas"; //llamado a la API REST y se trae todos los productos
            const respuesta = await fetch(url); 
            cajas = await respuesta.json(); 
            console.log(cajas);
        } catch (error) {
            console.log(error);
        }
      })();*/

     //////////////////  TABLA //////////////////////
    tablaPedidosGuardados = ($('#tablaPedidosGuardados') as any).DataTable({
      ...configdatatables,
      responsive: {
        details: false
      },
      columnDefs: [
        { targets: 0, className: 'all dtr-control', responsivePriority: 1 },
        { targets: 1, className: 'all', responsivePriority: 2 },
        { targets: 2, responsivePriority: 3 },
        { targets: 3, responsivePriority: 4 },
        { targets: 4, responsivePriority: 9 },
        { targets: 5, responsivePriority: 8 },
        { targets: 6, responsivePriority: 5 },
        { targets: 7, responsivePriority: 7 },
        { targets: 8, responsivePriority: 6 },
        { targets: 9, responsivePriority: 10, orderable: false, searchable: false }
      ]
    });

    document.querySelector('#tablaPedidosGuardados')?.addEventListener("click", (e)=>{ //evento click sobre toda la tabla
      const target = e.target as HTMLElement;
      const expandCell = target.closest('td.dtr-control') as HTMLTableCellElement|null;
      if(expandCell && window.innerWidth <= 768){
        const row = expandCell.closest('tr');
        if(row){
          const dataTableRow = (tablaPedidosGuardados as any).row(row);
          if(dataTableRow.child.isShown()){
            dataTableRow.child.hide();
            row.classList.remove('parent');
          }else{
            const headers = Array.from(document.querySelectorAll<HTMLTableCellElement>('#tablaPedidosGuardados thead th'));
            const cells = Array.from(row.querySelectorAll<HTMLTableCellElement>('td'));
            const detailRows = cells.slice(2).map((cell, index) => {
              const title = headers[index + 2]?.textContent?.trim() || '';
              return `<li><span class="dtr-title">${title}</span><span class="dtr-data">${cell.innerHTML}</span></li>`;
            }).join('');
            dataTableRow.child(`<ul class="dtr-details">${detailRows}</ul>`, 'child').show();
            row.classList.add('parent');
          }
        }
      }
      if(target?.classList.contains("eliminarPedidoGuardado")||target.parentElement?.classList.contains("eliminarPedidoGuardado"))eliminarPedidoGuardado(e);
    });


    ////////////////////  Eliminar cotizacion por completo del sistema  //////////////////////
    function eliminarPedidoGuardado(e:Event){
      const target = e.target as HTMLElement;
      const acciones = target.closest('.pg-actions, .acciones-btns') as HTMLElement|null;
      let idpedidoguardado = acciones?.id || target.parentElement!.id, info = (tablaPedidosGuardados as any).page.info();
      if(target.tagName === 'I' && !acciones)idpedidoguardado = target.parentElement!.parentElement!.id;
      indiceFila = (tablaPedidosGuardados as any).row((e.target as HTMLElement).closest('tr')).index();
      Swal.fire({
          customClass: {confirmButton: 'sweetbtnconfirm', cancelButton: 'sweetbtncancel'},
          icon: 'question',
          title: `Desea eliminar la orden guardado #${idpedidoguardado}?`,
          text: "La orden/cotizacion guardado sera eliminado definitivamente del sistema.",
          showCancelButton: true,
          confirmButtonText: 'Si',
          cancelButtonText: 'No',
      }).then((result:any) => {
          if (result.isConfirmed) {
              (async ()=>{ 
                  const datos = new FormData();
                  datos.append('id', idpedidoguardado);
                  try {
                      const url = "/admin/api/eliminarPedidoGuardado"; // llama controlador cajacontrolador.php
                      const respuesta = await fetch(url, {method: 'POST', body: datos}); 
                      const resultado = await respuesta.json();  
                      if(resultado.exito !== undefined){
                        (tablaPedidosGuardados as any).row(indiceFila).remove().draw(); 
                        (tablaPedidosGuardados as any).page(info.page).draw('page'); 
                        Swal.fire(resultado.exito[0], '', 'success')
                      }else{
                          Swal.fire(resultado.error[0], '', 'error')
                      }
                  } catch (error) {
                      console.log(error);
                  }
              })();//cierre de async()
          }
      });
    }
    

    function limpiarformdialog(){
      (document.querySelector('#formCrearUpdateCaja') as HTMLFormElement)?.reset();
    }

  }

})();
