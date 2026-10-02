(():void=>{

  if(document.querySelector('.pendienteDespacho')){

    Object.assign(configdatatables25reg, { order: [[ 1, 'desc' ]] });
    const tablaDespachosPendientes = ($('#tabladespachosPendientes') as any).DataTable({
      ...configdatatables25reg,
      responsive: {
        details: false
      },
      columnDefs: [
        { targets: 0, className: 'all dtr-control', responsivePriority: 1 },
        { targets: 1, className: 'all', responsivePriority: 2 },
        { targets: 2, responsivePriority: 3 },
        { targets: 3, responsivePriority: 4 },
        { targets: 4, responsivePriority: 5 },
        { targets: 5, responsivePriority: 6 },
        { targets: 6, responsivePriority: 9 },
        { targets: 7, responsivePriority: 7 },
        { targets: 8, responsivePriority: 8 },
        { targets: 9, responsivePriority: 10, orderable: false, searchable: false }
      ]
    });

    document.querySelector('#tabladespachosPendientes')?.addEventListener('click', (e)=>{
      const target = e.target as HTMLElement;
      const expandCell = target.closest('td') as HTMLTableCellElement|null;
      if(!expandCell || expandCell.cellIndex !== 0 || window.innerWidth > 768)return;

      const row = expandCell.closest('tr');
      if(!row)return;

      const dataTableRow = (tablaDespachosPendientes as any).row(row);
      if(dataTableRow.child.isShown()){
        dataTableRow.child.hide();
        row.classList.remove('parent');
        return;
      }

      const headers = Array.from(document.querySelectorAll<HTMLTableCellElement>('#tabladespachosPendientes thead th'));
      const cells = Array.from(row.querySelectorAll<HTMLTableCellElement>('td'));
      const detailRows = cells.slice(2).map((cell, index) => {
        const title = headers[index + 2]?.textContent?.trim() || '';
        return `<li><span class="dtr-title">${title}</span><span class="dtr-data">${cell.innerHTML}</span></li>`;
      }).join('');

      dataTableRow.child(`<ul class="dtr-details">${detailRows}</ul>`, 'child').show();
      row.classList.add('parent');
    });

  }

})();
