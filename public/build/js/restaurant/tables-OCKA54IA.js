async function y(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),a=await n.json();if(!n.ok||!a.success||!a.occupation)throw new Error(a.message??"No fue posible consultar la atenci\xF3n.");return a.occupation}async function L(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var l,w,g,C,I;function B(){window.clearTimeout(I),w.classList.remove("hidden"),l.inert=!1,requestAnimationFrame(()=>{l.classList.remove("translate-x-full")}),l.setAttribute("aria-hidden","false")}function v(){l.getAttribute("aria-hidden")!=="true"&&(++m,l.classList.add("translate-x-full"),l.setAttribute("aria-hidden","true"),l.inert=!0,I=window.setTimeout(()=>{w.classList.add("hidden")},300))}function _(){g.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function x(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function V(e){let t=e.clientName?x(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",a=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",o=e.observations?x(e.observations):"Sin observaciones";g.innerHTML=`
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
                    <p class="font-medium text-slate-900">${x(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${x(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,g.appendChild(t)}function z(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,g.appendChild(t)}var m=0;function H(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),a=document.getElementById("activeOccupationActions");!e||!t||!n||!a||(l=e,w=t,g=n,C=a,l.inert=!0,l.addEventListener("click",o=>{o.target.closest("[data-close-active-occupation]")&&v()}),w.addEventListener("click",v),document.addEventListener("keydown",o=>{o.key==="Escape"&&l.getAttribute("aria-hidden")==="false"&&v()}))}async function $(e){if(!e)return;let t=++m;B(),_(),C.classList.add("hidden");try{let n=await y(e);if(t!==m)return;V(n);try{let a=await L(n.occupationId);if(t!==m)return;F(a.accounts)}catch(a){if(t!==m)return;z(a instanceof Error?a.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==m)return;g.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${x(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function d(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}async function P(){let t=await fetch("/admin/api/inventory/getAllcategorias");if(!t.ok)throw new Error("No fue posible consultar las categorias.");let n=await t.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}async function k(e=1,t=null){let n=new URLSearchParams({page:String(e)});t!=null&&n.set("categoriaId",String(t));let a=`/admin/api/inventory/listSimpleProducts?${n.toString()}`,o=await fetch(a),i=await o.json();if(!o.ok||!i.success||!i.data||!Array.isArray(i.data.products))throw new Error("No fue posible consultar las productos.");return i.data}var c,O,U,D,E=0,S=0,N,u=[],f=null,A=document.querySelector("#atencionMesaText");function G(){window.clearTimeout(N),O.classList.remove("hidden"),c.inert=!1,c.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>c.classList.remove("translate-x-full"))}function h(){if(c.getAttribute("aria-hidden")==="true")return;++E;let e=document.activeElement;e instanceof HTMLElement&&c.contains(e)&&e.blur(),c.classList.add("translate-x-full"),c.inert=!0,c.setAttribute("aria-hidden","true"),N=window.setTimeout(()=>O.classList.add("hidden"),300)}function J(e){let t=u.find(a=>a.id===f)??null,n=u.map(a=>`
        <option value="${a.id}" ${a.id===f?"selected":""}>
            ${d(a.name)}
        </option>
    `).join("")}async function q(e,t){if(!e||!c)return;let n=++E;u=[],f=null,D.textContent=t,G();try{let a=await y(e);if(n!==E)return;let o=await L(a.occupationId);if(n!==E)return;u=o.accounts,f=u.find(i=>i.status==="abierta")?.id??u[0]?.id??null,A.classList.add("bg-emerald-50","text-emerald-700"),A.textContent="En atenci\xF3n",J(a)}catch(a){if(n!==E)return;let o=a instanceof Error?a.message:"No fue posible consultar la mesa.";A.classList.add("bg-red-50","text-red-700"),A.textContent=`${d(o)}`}}function j(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),a=document.getElementById("orderWorkspaceTitle");!e||!t||!n||!a||(K(),c=e,O=t,U=n,D=a,c.inert=!0,c.addEventListener("click",o=>{o.target.closest("[data-close-order-workspace]")&&h()}),c.addEventListener("change",o=>{let i=o.target;if(!i.matches("[data-order-workspace-account]"))return;f=Number(i.value);let r=u.find(b=>b.id===f),s=c.querySelector("[data-order-workspace-account-info]");r&&s&&(s.textContent=`Estado: ${r.status} \xB7 ${r.invoiceId===null?"Sin orden todav\xEDa":`Orden #${r.invoiceId}`}`)}),O.addEventListener("click",h),document.addEventListener("keydown",o=>{o.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&h()}),R())}async function K(){let e=await P(),t=document.querySelector("#divCategorias");t.innerHTML=`
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
    `,t.addEventListener("click",n=>{let a=n.target.closest("button[data-categoria-id]");if(!a||!t.contains(a))return;let o=a.dataset.categoriaId,i=o==="all"?null:Number(o);i!==null&&(!Number.isInteger(i)||i<=0)||(t.querySelectorAll("button[data-categoria-id]").forEach(r=>{let s=r===a;r.setAttribute("aria-pressed",String(s)),r.classList.toggle("border-indigo-600",s),r.classList.toggle("bg-indigo-600",s),r.classList.toggle("text-white",s),r.classList.toggle("border-slate-200",!s),r.classList.toggle("bg-white",!s),r.classList.toggle("text-slate-600",!s),r.classList.toggle("hover:bg-slate-50",!s)}),R(i))})}async function R(e=null){let t=document.querySelector("#catalogoPaginador"),n=document.querySelector("#catalogProductGrid"),a=document.querySelector("#catalogProductCount"),o=document.querySelector("#catalogProductStatus");if(!n||!a||!o)return;let i=++S;n.replaceChildren(),a.textContent="Cargando...",o.textContent="Cargando productos...";try{let r=await k(1,e);if(i!==S)return;a.textContent=`${r.products.length} en esta p\xE1gina`,o.textContent=r.products.length>0?`Mostrando p\xE1gina ${r.page}`:"No hay productos vendibles en esta sucursal.",n.innerHTML=r.products.map(s=>{let b=d(s.nombre),T=s.foto&&s.foto!=="null"&&s.foto!=="undefined"?s.foto:null,W=Number(s.precioVenta).toLocaleString();return`
                <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${T!=null?"":"grid place-items-center"}">
                        ${T!=null?`<img src="/build/img/${d(T)}" alt="${b}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${b}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${d(W)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${b}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join(""),t?.insertAdjacentHTML("afterend",`
            <div class="mt-5 text-center">
                <button
                    id="catalogLoadMore"
                    type="button"
                    aria-controls="catalogProductGrid"
                    class="hidden rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-wait disabled:opacity-60">
                    Cargar m\xE1s
                </button>
            </div>`)}catch(r){a.textContent="Sin cargar",o.textContent=r instanceof Error?r.message:"No fue posible cargar los productos."}}function le(){H(),j(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),a=n?.closest("[data-table-id]");if(!n||!a)return;let o=Number(a.dataset.resourceId);o&&(n.hasAttribute("data-open-active-detail")?(h(),$(o)):(v(),q(o,a.dataset.tableName??"Mesa")))})}export{le as initTables};
//# sourceMappingURL=tables-OCKA54IA.js.map
