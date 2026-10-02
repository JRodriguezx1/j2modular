(()=>{

    if(!document.querySelector('.paramSistem'))return;

    function inicializarBuscadorParametrosSistema():void{
        const buscador = document.querySelector('#buscarParametroSistema') as HTMLInputElement|null;
        const limpiar = document.querySelector('#limpiarBusquedaParametroSistema') as HTMLButtonElement|null;
        const resultado = document.querySelector('#resultadoBusquedaParametroSistema') as HTMLElement|null;
        const contenedor = document.querySelector('.config-system-content') as HTMLElement|null;

        if(!buscador || !contenedor)return;

        const sistema = contenedor.closest('.config-system') as HTMLElement|null;
        const paneles = Array.from(contenedor.querySelectorAll('.accordion_tab_content')) as HTMLElement[];

        const normalizarTexto = (texto:string):string => texto
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();

        const obtenerPanel = (elemento:Element):HTMLElement|null => elemento.closest('.accordion_tab_content') as HTMLElement|null;

        const obtenerNumeroPanel = (panel:HTMLElement|null):string|null => {
            if(!panel)return null;
            const claseContenido = Array.from(panel.classList).find((clase)=>/^contenido\d+$/.test(clase));
            return claseContenido ? claseContenido.replace('contenido', '') : null;
        };

        const camposPorContenedor = new Map<HTMLElement, {
            contenedorCampo: HTMLElement,
            panel: HTMLElement,
            numeroPanel: string,
            textos: string[]
        }>();

        Array.from(contenedor.querySelectorAll('label')).forEach((label)=>{
            const etiqueta = label as HTMLElement;
            const fieldId = etiqueta.getAttribute('for') || '';
            const input = fieldId ? document.getElementById(fieldId) as HTMLInputElement|null : null;
            const contenedorCampo = (etiqueta.closest('.formulario__campo') || etiqueta.parentElement) as HTMLElement|null;
            const panel = obtenerPanel(etiqueta);
            const numeroPanel = obtenerNumeroPanel(panel);

            if(!contenedorCampo || !panel || !numeroPanel)return;

            const textoBusqueda = [
            etiqueta.innerText,
            input?.placeholder || '',
            input?.name || '',
            input?.id || ''
            ].join(' ');

            const campoExistente = camposPorContenedor.get(contenedorCampo);
            if(campoExistente){
            campoExistente.textos.push(textoBusqueda);
            return;
            }

            camposPorContenedor.set(contenedorCampo, {
            contenedorCampo,
            panel,
            numeroPanel,
            textos: [textoBusqueda]
            });
        });

        const campos = Array.from(camposPorContenedor.values()).map((campo)=>({
            contenedorCampo: campo.contenedorCampo,
            panel: campo.panel,
            numeroPanel: campo.numeroPanel,
            textoBusqueda: normalizarTexto([
            campo.contenedorCampo.innerText,
            ...campo.textos
            ].join(' '))
        }));

        const limpiarFiltro = ():void => {
            limpiar?.classList.add('hidden');
            sistema?.classList.remove('param-search-active');
            paneles.forEach((panel)=>panel.classList.remove('config-param-panel-match'));
            campos.forEach((campo)=>{
            campo.contenedorCampo?.classList.remove('config-param-hidden', 'config-param-match');
            });
            if(resultado)resultado.textContent = '';
        };

        const aplicarFiltro = ():void => {
            const termino = normalizarTexto(buscador.value);
            if(!termino){
            limpiarFiltro();
            return;
            }

            limpiar?.classList.remove('hidden');
            sistema?.classList.add('param-search-active');
            const coincidencias = campos.filter((campo)=>campo.textoBusqueda.includes(termino));
            const panelesConCoincidencias = new Set(coincidencias.map((campo)=>campo.panel));

            paneles.forEach((panel)=>{
            panel.classList.toggle('config-param-panel-match', panelesConCoincidencias.has(panel));
            });

            campos.forEach((campo)=>{
            const coincide = campo.textoBusqueda.includes(termino);
            campo.contenedorCampo?.classList.toggle('config-param-hidden', !coincide);
            campo.contenedorCampo?.classList.toggle('config-param-match', coincide);
            });

            const primeraCoincidencia = coincidencias[0];
            if(!primeraCoincidencia || !primeraCoincidencia.numeroPanel){
            if(resultado)resultado.textContent = '0 resultados';
            return;
            }

            if(resultado){
            const totalResultados = coincidencias.length;
            resultado.textContent = totalResultados === 1 ? '1 resultado' : `${totalResultados} resultados`;
            }

            const radio = document.querySelector(`#btn${primeraCoincidencia.numeroPanel}`) as HTMLInputElement|null;
            if(radio && !radio.checked){
            radio.checked = true;
            radio.dispatchEvent(new Event('change', {bubbles: true}));
            }

            window.setTimeout(()=>{
            primeraCoincidencia.contenedorCampo?.scrollIntoView({behavior: 'smooth', block: 'center'});
            }, 80);
        };

        buscador.addEventListener('input', aplicarFiltro);
        sistema?.querySelectorAll('.btnsetup .config-tab').forEach((tab)=>{
            tab.addEventListener('click', ()=>{
            if(!buscador.value)return;
            buscador.value = '';
            limpiarFiltro();
            });
        });
        limpiar?.addEventListener('click', ()=>{
            buscador.value = '';
            buscador.focus();
            limpiarFiltro();
        });
        }

        inicializarBuscadorParametrosSistema();

})();
