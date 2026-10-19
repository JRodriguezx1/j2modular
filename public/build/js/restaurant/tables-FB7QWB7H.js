async function E(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),r=await n.json();if(!n.ok||!r.success||!r.occupation)throw new Error(r.message??"No fue posible consultar la atenci\xF3n.");return r.occupation}async function h(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var i,y,p,C,$;function j(){window.clearTimeout($),y.classList.remove("hidden"),i.inert=!1,requestAnimationFrame(()=>{i.classList.remove("translate-x-full")}),i.setAttribute("aria-hidden","false")}function f(){i.getAttribute("aria-hidden")!=="true"&&(++m,i.classList.add("translate-x-full"),i.setAttribute("aria-hidden","true"),i.inert=!0,$=window.setTimeout(()=>{y.classList.add("hidden")},300))}function q(){p.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function v(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function R(e){let t=e.clientName?v(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",r=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",o=e.observations?v(e.observations):"Sin observaciones";p.innerHTML=`
        <div class="space-y-6">
            <div class="rounded-2xl bg-emerald-50 p-4">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-600">
                        restaurant
                    </span>

                    <div>
                        <p class="text-xs font-medium uppercase text-emerald-700">
                            Estado
                        </p>
                        <p class="font-semibold text-emerald-900">
                            Atenci\xF3n en curso
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                ${u("Cliente",t)}
                ${u("Personas",n)}
                ${u("Origen",r)}
                ${u("Inicio",T(e.startDate))}
                ${u("Fin estimado",e.estimatedEndDate?T(e.estimatedEndDate):"\u2014")}
                ${u("Observaciones",o)}
            </div>
        </div>`}function u(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function T(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function W(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${v(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${v(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,p.appendChild(t)}function B(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,p.appendChild(t)}var m=0;function I(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),r=document.getElementById("activeOccupationActions");!e||!t||!n||!r||(i=e,y=t,p=n,C=r,i.inert=!0,i.addEventListener("click",o=>{o.target.closest("[data-close-active-occupation]")&&f()}),y.addEventListener("click",f),document.addEventListener("keydown",o=>{o.key==="Escape"&&i.getAttribute("aria-hidden")==="false"&&f()}))}async function M(e){if(!e)return;let t=++m;j(),q(),C.classList.add("hidden");try{let n=await E(e);if(t!==m)return;R(n);try{let r=await h(n.occupationId);if(t!==m)return;W(r.accounts)}catch(r){if(t!==m)return;B(r instanceof Error?r.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==m)return;p.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${v(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function l(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}async function H(){let t=await fetch("/admin/api/inventory/getAllcategorias");if(!t.ok)throw new Error("No fue posible consultar las categorias.");let n=await t.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}async function P(e=1,t=null){let n=new URLSearchParams({page:String(e)});t!=null&&n.set("categoriaId",String(t));let r=`/admin/api/inventory/listSimpleProducts?${n.toString()}`,o=await fetch(r),s=await o.json();if(!o.ok||!s.success||!s.data||!Array.isArray(s.data.products))throw new Error("No fue posible consultar las productos.");return s.data}var a,w,_,k,b=0,S,d=[],g=null,A=document.querySelector("#atencionMesaText");function F(){window.clearTimeout(S),w.classList.remove("hidden"),a.inert=!1,a.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>a.classList.remove("translate-x-full"))}function x(){if(a.getAttribute("aria-hidden")==="true")return;++b;let e=document.activeElement;e instanceof HTMLElement&&a.contains(e)&&e.blur(),a.classList.add("translate-x-full"),a.inert=!0,a.setAttribute("aria-hidden","true"),S=window.setTimeout(()=>w.classList.add("hidden"),300)}function V(e){let t=d.find(r=>r.id===g)??null,n=d.map(r=>`
        <option value="${r.id}" ${r.id===g?"selected":""}>
            ${l(r.name)}
        </option>
    `).join("")}async function D(e,t){if(!e||!a)return;let n=++b;d=[],g=null,k.textContent=t,F();try{let r=await E(e);if(n!==b)return;let o=await h(r.occupationId);if(n!==b)return;d=o.accounts,g=d.find(s=>s.status==="abierta")?.id??d[0]?.id??null,A.classList.add("bg-emerald-50","text-emerald-700"),A.textContent="En atenci\xF3n",V(r)}catch(r){if(n!==b)return;let o=r instanceof Error?r.message:"No fue posible consultar la mesa.";A.classList.add("bg-red-50","text-red-700"),A.textContent=`${l(o)}`}}function N(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),r=document.getElementById("orderWorkspaceTitle");!e||!t||!n||!r||(z(),a=e,w=t,_=n,k=r,a.inert=!0,a.addEventListener("click",o=>{o.target.closest("[data-close-order-workspace]")&&x()}),a.addEventListener("change",o=>{let s=o.target;if(!s.matches("[data-order-workspace-account]"))return;g=Number(s.value);let c=d.find(L=>L.id===g),O=a.querySelector("[data-order-workspace-account-info]");c&&O&&(O.textContent=`Estado: ${c.status} \xB7 ${c.invoiceId===null?"Sin orden todav\xEDa":`Orden #${c.invoiceId}`}`)}),w.addEventListener("click",x),document.addEventListener("keydown",o=>{o.key==="Escape"&&a.getAttribute("aria-hidden")==="false"&&x()}),U())}async function z(){let e=await H(),t=document.querySelector("#divCategorias"),n="";e.forEach(r=>{n+=`
            <button
                id="idCategoria_${r.id}"
                type="button" 
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${l(r.nombre)}
            </button>`}),t.innerHTML=n}async function U(){let e=document.querySelector("#catalogProductGrid"),t=document.querySelector("#catalogProductCount"),n=document.querySelector("#catalogProductStatus");if(!(!e||!t||!n)){e.replaceChildren(),t.textContent="Cargando...",n.textContent="Cargando productos...";try{let r=await P();t.textContent=`${r.products.length} en esta p\xE1gina`,n.textContent=r.products.length>0?`Mostrando p\xE1gina ${r.page}`:"No hay productos vendibles en esta sucursal.",e.innerHTML=r.products.map(o=>{let s=l(o.nombre),c=o.foto&&o.foto!=="null"&&o.foto!=="undefined"?o.foto:null,L=Number(o.precioVenta).toLocaleString();return`
                <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${c!=null?"":"grid place-items-center"}">
                        ${c!=null?`<img src="/build/img/${l(c)}" alt="${s}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${s}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${l(L)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${s}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("")}catch(r){t.textContent="Sin cargar",n.textContent=r instanceof Error?r.message:"No fue posible cargar los productos."}}}function ae(){I(),N(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),r=n?.closest("[data-table-id]");if(!n||!r)return;let o=Number(r.dataset.resourceId);o&&(n.hasAttribute("data-open-active-detail")?(x(),M(o)):(f(),D(o,r.dataset.tableName??"Mesa")))})}export{ae as initTables};
//# sourceMappingURL=tables-FB7QWB7H.js.map
