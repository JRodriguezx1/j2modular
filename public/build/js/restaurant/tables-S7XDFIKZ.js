async function L(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),a=await n.json();if(!n.ok||!a.success||!a.occupation)throw new Error(a.message??"No fue posible consultar la atenci\xF3n.");return a.occupation}async function w(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var l,A,f,C,I;function W(){window.clearTimeout(I),A.classList.remove("hidden"),l.inert=!1,requestAnimationFrame(()=>{l.classList.remove("translate-x-full")}),l.setAttribute("aria-hidden","false")}function x(){l.getAttribute("aria-hidden")!=="true"&&(++g,l.classList.add("translate-x-full"),l.setAttribute("aria-hidden","true"),l.inert=!0,I=window.setTimeout(()=>{A.classList.add("hidden")},300))}function _(){f.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function E(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function V(e){let t=e.clientName?E(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",a=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",o=e.observations?E(e.observations):"Sin observaciones";f.innerHTML=`
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
                ${p("Cliente",t)}
                ${p("Personas",n)}
                ${p("Origen",a)}
                ${p("Inicio",M(e.startDate))}
                ${p("Fin estimado",e.estimatedEndDate?M(e.estimatedEndDate):"\u2014")}
                ${p("Observaciones",o)}
            </div>
        </div>`}function p(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function M(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function F(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${E(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${E(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,f.appendChild(t)}function z(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,f.appendChild(t)}var g=0;function P(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),a=document.getElementById("activeOccupationActions");!e||!t||!n||!a||(l=e,A=t,f=n,C=a,l.inert=!0,l.addEventListener("click",o=>{o.target.closest("[data-close-active-occupation]")&&x()}),A.addEventListener("click",x),document.addEventListener("keydown",o=>{o.key==="Escape"&&l.getAttribute("aria-hidden")==="false"&&x()}))}async function H(e){if(!e)return;let t=++g;W(),_(),C.classList.add("hidden");try{let n=await L(e);if(t!==g)return;V(n);try{let a=await w(n.occupationId);if(t!==g)return;F(a.accounts)}catch(a){if(t!==g)return;z(a instanceof Error?a.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==g)return;f.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${E(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function d(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}async function $(){let t=await fetch("/admin/api/inventory/getAllcategorias");if(!t.ok)throw new Error("No fue posible consultar las categorias.");let n=await t.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}async function k(e=1,t=null){let n=new URLSearchParams({page:String(e)});t!=null&&n.set("categoriaId",String(t));let a=`/admin/api/inventory/listSimpleProducts?${n.toString()}`,o=await fetch(a),r=await o.json();if(!o.ok||!r.success||!r.data||!Array.isArray(r.data.products))throw new Error("No fue posible consultar las productos.");return r.data}var c,T,U,D,h=0,S=0,N,u=[],v=null,G=0,J=null,K=!1,Q=!1,O=document.querySelector("#atencionMesaText");function X(){window.clearTimeout(N),T.classList.remove("hidden"),c.inert=!1,c.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>c.classList.remove("translate-x-full"))}function y(){if(c.getAttribute("aria-hidden")==="true")return;++h;let e=document.activeElement;e instanceof HTMLElement&&c.contains(e)&&e.blur(),c.classList.add("translate-x-full"),c.inert=!0,c.setAttribute("aria-hidden","true"),N=window.setTimeout(()=>T.classList.add("hidden"),300)}function Y(e){let t=u.find(a=>a.id===v)??null,n=u.map(a=>`
        <option value="${a.id}" ${a.id===v?"selected":""}>
            ${d(a.name)}
        </option>
    `).join("")}async function q(e,t){if(!e||!c)return;let n=++h;u=[],v=null,D.textContent=t,X();try{let a=await L(e);if(n!==h)return;let o=await w(a.occupationId);if(n!==h)return;u=o.accounts,v=u.find(r=>r.status==="abierta")?.id??u[0]?.id??null,O.classList.add("bg-emerald-50","text-emerald-700"),O.textContent="En atenci\xF3n",Y(a)}catch(a){if(n!==h)return;let o=a instanceof Error?a.message:"No fue posible consultar la mesa.";O.classList.add("bg-red-50","text-red-700"),O.textContent=`${d(o)}`}}function R(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),a=document.getElementById("orderWorkspaceTitle"),o=document.querySelector("#catalogLoadMore");!e||!t||!n||!a||(Z(),c=e,T=t,U=n,D=a,c.inert=!0,c.addEventListener("click",r=>{r.target.closest("[data-close-order-workspace]")&&y()}),c.addEventListener("change",r=>{let s=r.target;if(!s.matches("[data-order-workspace-account]"))return;v=Number(s.value);let i=u.find(b=>b.id===v),m=c.querySelector("[data-order-workspace-account-info]");i&&m&&(m.textContent=`Estado: ${i.status} \xB7 ${i.invoiceId===null?"Sin orden todav\xEDa":`Orden #${i.invoiceId}`}`)}),T.addEventListener("click",y),document.addEventListener("keydown",r=>{r.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&y()}),o?.addEventListener("click",()=>{ee()}),j())}async function Z(){let e=await $(),t=document.querySelector("#divCategorias");t.innerHTML=`
        <button
            type="button"
            data-categoria-id="all"
            aria-pressed="true"
            class="btncateogira shrink-0 rounded-lg border border-indigo-600 bg-indigo-600 px-3 py-2 font-medium text-white">
            Todas
        </button>
        ${e.map(n=>`
            <button
                type="button"
                data-categoria-id="${d(n.id)}"
                aria-pressed="false"
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${d(n.nombre)}
            </button>
        `).join("")}
    `,t.addEventListener("click",n=>{let a=n.target.closest("button[data-categoria-id]");if(!a||!t.contains(a))return;let o=a.dataset.categoriaId,r=o==="all"?null:Number(o);r!==null&&(!Number.isInteger(r)||r<=0)||(t.querySelectorAll("button[data-categoria-id]").forEach(s=>{let i=s===a;s.setAttribute("aria-pressed",String(i)),s.classList.toggle("border-indigo-600",i),s.classList.toggle("bg-indigo-600",i),s.classList.toggle("text-white",i),s.classList.toggle("border-slate-200",!i),s.classList.toggle("bg-white",!i),s.classList.toggle("text-slate-600",!i),s.classList.toggle("hover:bg-slate-50",!i)}),j(r))})}async function ee(){Q||!K||await cargarPaginaProductos(G+1,J,!1)}async function j(e=null){let t=document.querySelector("#catalogoPaginador"),n=document.querySelector("#catalogProductGrid"),a=document.querySelector("#catalogProductCount"),o=document.querySelector("#catalogProductStatus");if(!n||!a||!o)return;let r=++S;n.replaceChildren(),a.textContent="Cargando...",o.textContent="Cargando productos...";try{let s=await k(1,e);if(r!==S)return;a.textContent=`${s.products.length} en esta p\xE1gina`,o.textContent=s.products.length>0?`Mostrando p\xE1gina ${s.page}`:"No hay productos vendibles en esta sucursal.",n.innerHTML=s.products.map(i=>{let m=d(i.nombre),b=i.foto&&i.foto!=="null"&&i.foto!=="undefined"?i.foto:null,B=Number(i.precioVenta).toLocaleString();return`
                <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${b!=null?"":"grid place-items-center"}">
                        ${b!=null?`<img src="/build/img/${d(b)}" alt="${m}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${m}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${d(B)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${m}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("")}catch(s){a.textContent="Sin cargar",o.textContent=s instanceof Error?s.message:"No fue posible cargar los productos."}}function ge(){P(),R(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),a=n?.closest("[data-table-id]");if(!n||!a)return;let o=Number(a.dataset.resourceId);o&&(n.hasAttribute("data-open-active-detail")?(y(),H(o)):(x(),q(o,a.dataset.tableName??"Mesa")))})}export{ge as initTables};
//# sourceMappingURL=tables-S7XDFIKZ.js.map
