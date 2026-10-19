async function y(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),r=await n.json();if(!n.ok||!r.success||!r.occupation)throw new Error(r.message??"No fue posible consultar la atenci\xF3n.");return r.occupation}async function A(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var l,L,g,C,I;function W(){window.clearTimeout(I),L.classList.remove("hidden"),l.inert=!1,requestAnimationFrame(()=>{l.classList.remove("translate-x-full")}),l.setAttribute("aria-hidden","false")}function v(){l.getAttribute("aria-hidden")!=="true"&&(++m,l.classList.add("translate-x-full"),l.setAttribute("aria-hidden","true"),l.inert=!0,I=window.setTimeout(()=>{L.classList.add("hidden")},300))}function B(){g.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function x(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function _(e){let t=e.clientName?x(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",r=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",a=e.observations?x(e.observations):"Sin observaciones";g.innerHTML=`
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
                ${p("Origen",r)}
                ${p("Inicio",T(e.startDate))}
                ${p("Fin estimado",e.estimatedEndDate?T(e.estimatedEndDate):"\u2014")}
                ${p("Observaciones",a)}
            </div>
        </div>`}function p(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function T(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function V(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${x(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${x(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,g.appendChild(t)}function F(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,g.appendChild(t)}var m=0;function M(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),r=document.getElementById("activeOccupationActions");!e||!t||!n||!r||(l=e,L=t,g=n,C=r,l.inert=!0,l.addEventListener("click",a=>{a.target.closest("[data-close-active-occupation]")&&v()}),L.addEventListener("click",v),document.addEventListener("keydown",a=>{a.key==="Escape"&&l.getAttribute("aria-hidden")==="false"&&v()}))}async function $(e){if(!e)return;let t=++m;W(),B(),C.classList.add("hidden");try{let n=await y(e);if(t!==m)return;_(n);try{let r=await A(n.occupationId);if(t!==m)return;V(r.accounts)}catch(r){if(t!==m)return;F(r instanceof Error?r.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==m)return;g.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${x(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function d(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}async function H(){let t=await fetch("/admin/api/inventory/getAllcategorias");if(!t.ok)throw new Error("No fue posible consultar las categorias.");let n=await t.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}async function k(e=1,t=null){let n=new URLSearchParams({page:String(e)});t!=null&&n.set("categoriaId",String(t));let r=`/admin/api/inventory/listSimpleProducts?${n.toString()}`,a=await fetch(r),o=await a.json();if(!a.ok||!o.success||!o.data||!Array.isArray(o.data.products))throw new Error("No fue posible consultar las productos.");return o.data}var c,O,z,S,E=0,P=0,N,u=[],f=null,w=document.querySelector("#atencionMesaText");function U(){window.clearTimeout(N),O.classList.remove("hidden"),c.inert=!1,c.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>c.classList.remove("translate-x-full"))}function h(){if(c.getAttribute("aria-hidden")==="true")return;++E;let e=document.activeElement;e instanceof HTMLElement&&c.contains(e)&&e.blur(),c.classList.add("translate-x-full"),c.inert=!0,c.setAttribute("aria-hidden","true"),N=window.setTimeout(()=>O.classList.add("hidden"),300)}function G(e){let t=u.find(r=>r.id===f)??null,n=u.map(r=>`
        <option value="${r.id}" ${r.id===f?"selected":""}>
            ${d(r.name)}
        </option>
    `).join("")}async function D(e,t){if(!e||!c)return;let n=++E;u=[],f=null,S.textContent=t,U();try{let r=await y(e);if(n!==E)return;let a=await A(r.occupationId);if(n!==E)return;u=a.accounts,f=u.find(o=>o.status==="abierta")?.id??u[0]?.id??null,w.classList.add("bg-emerald-50","text-emerald-700"),w.textContent="En atenci\xF3n",G(r)}catch(r){if(n!==E)return;let a=r instanceof Error?r.message:"No fue posible consultar la mesa.";w.classList.add("bg-red-50","text-red-700"),w.textContent=`${d(a)}`}}function q(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),r=document.getElementById("orderWorkspaceTitle");!e||!t||!n||!r||(J(),c=e,O=t,z=n,S=r,c.inert=!0,c.addEventListener("click",a=>{a.target.closest("[data-close-order-workspace]")&&h()}),c.addEventListener("change",a=>{let o=a.target;if(!o.matches("[data-order-workspace-account]"))return;f=Number(o.value);let s=u.find(b=>b.id===f),i=c.querySelector("[data-order-workspace-account-info]");s&&i&&(i.textContent=`Estado: ${s.status} \xB7 ${s.invoiceId===null?"Sin orden todav\xEDa":`Orden #${s.invoiceId}`}`)}),O.addEventListener("click",h),document.addEventListener("keydown",a=>{a.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&h()}),R())}async function J(){let e=await H(),t=document.querySelector("#divCategorias");t.innerHTML=`
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
    `,t.addEventListener("click",n=>{let r=n.target.closest("button[data-categoria-id]");if(!r||!t.contains(r))return;let a=r.dataset.categoriaId,o=a==="all"?null:Number(a);o!==null&&(!Number.isInteger(o)||o<=0)||(t.querySelectorAll("button[data-categoria-id]").forEach(s=>{let i=s===r;s.setAttribute("aria-pressed",String(i)),s.classList.toggle("border-indigo-600",i),s.classList.toggle("bg-indigo-600",i),s.classList.toggle("text-white",i),s.classList.toggle("border-slate-200",!i),s.classList.toggle("bg-white",!i),s.classList.toggle("text-slate-600",!i),s.classList.toggle("hover:bg-slate-50",!i)}),R(o))})}async function R(e=null){let t=document.querySelector("#catalogProductGrid"),n=document.querySelector("#catalogProductCount"),r=document.querySelector("#catalogProductStatus");if(!t||!n||!r)return;let a=++P;t.replaceChildren(),n.textContent="Cargando...",r.textContent="Cargando productos...";try{let o=await k(1,e);if(a!==P)return;n.textContent=`${o.products.length} en esta p\xE1gina`,r.textContent=o.products.length>0?`Mostrando p\xE1gina ${o.page}`:"No hay productos vendibles en esta sucursal.",t.innerHTML=o.products.map(s=>{let i=d(s.nombre),b=s.foto&&s.foto!=="null"&&s.foto!=="undefined"?s.foto:null,j=Number(s.precioVenta).toLocaleString();return`
                <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${b!=null?"":"grid place-items-center"}">
                        ${b!=null?`<img src="/build/img/${d(b)}" alt="${i}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${i}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${d(j)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${i}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("")}catch(o){n.textContent="Sin cargar",r.textContent=o instanceof Error?o.message:"No fue posible cargar los productos."}}function ce(){M(),q(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),r=n?.closest("[data-table-id]");if(!n||!r)return;let a=Number(r.dataset.resourceId);a&&(n.hasAttribute("data-open-active-detail")?(h(),$(a)):(v(),D(a,r.dataset.tableName??"Mesa")))})}export{ce as initTables};
//# sourceMappingURL=tables-7L35DIYT.js.map
