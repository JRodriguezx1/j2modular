async function y(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),a=await n.json();if(!n.ok||!a.success||!a.occupation)throw new Error(a.message??"No fue posible consultar la atenci\xF3n.");return a.occupation}async function L(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var l,A,f,H,$;function _(){window.clearTimeout($),A.classList.remove("hidden"),l.inert=!1,requestAnimationFrame(()=>{l.classList.remove("translate-x-full")}),l.setAttribute("aria-hidden","false")}function b(){l.getAttribute("aria-hidden")!=="true"&&(++g,l.classList.add("translate-x-full"),l.setAttribute("aria-hidden","true"),l.inert=!0,$=window.setTimeout(()=>{A.classList.add("hidden")},300))}function V(){f.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function x(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function F(e){let t=e.clientName?x(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",a=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",o=e.observations?x(e.observations):"Sin observaciones";f.innerHTML=`
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
                ${m("Cliente",t)}
                ${m("Personas",n)}
                ${m("Origen",a)}
                ${m("Inicio",I(e.startDate))}
                ${m("Fin estimado",e.estimatedEndDate?I(e.estimatedEndDate):"\u2014")}
                ${m("Observaciones",o)}
            </div>
        </div>`}function m(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function I(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function z(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${x(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${x(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,f.appendChild(t)}function U(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,f.appendChild(t)}var g=0;function k(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),a=document.getElementById("activeOccupationActions");!e||!t||!n||!a||(l=e,A=t,f=n,H=a,l.inert=!0,l.addEventListener("click",o=>{o.target.closest("[data-close-active-occupation]")&&b()}),A.addEventListener("click",b),document.addEventListener("keydown",o=>{o.key==="Escape"&&l.getAttribute("aria-hidden")==="false"&&b()}))}async function P(e){if(!e)return;let t=++g;_(),V(),H.classList.add("hidden");try{let n=await y(e);if(t!==g)return;F(n);try{let a=await L(n.occupationId);if(t!==g)return;z(a.accounts)}catch(a){if(t!==g)return;U(a instanceof Error?a.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==g)return;f.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${x(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function u(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}async function S(){let t=await fetch("/admin/api/inventory/getAllcategorias");if(!t.ok)throw new Error("No fue posible consultar las categorias.");let n=await t.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}async function D(e=1,t=null){let n=new URLSearchParams({page:String(e)});t!=null&&n.set("categoriaId",String(t));let a=`/admin/api/inventory/listSimpleProducts?${n.toString()}`,o=await fetch(a),r=await o.json();if(!o.ok||!r.success||!r.data||!Array.isArray(r.data.products))throw new Error("No fue posible consultar las productos.");return r.data}var c,O,G,q,E=0,N=0,R,p=[],v=null,w=document.querySelector("#atencionMesaText");function J(){window.clearTimeout(R),O.classList.remove("hidden"),c.inert=!1,c.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>c.classList.remove("translate-x-full"))}function h(){if(c.getAttribute("aria-hidden")==="true")return;++E;let e=document.activeElement;e instanceof HTMLElement&&c.contains(e)&&e.blur(),c.classList.add("translate-x-full"),c.inert=!0,c.setAttribute("aria-hidden","true"),R=window.setTimeout(()=>O.classList.add("hidden"),300)}function K(e){let t=p.find(a=>a.id===v)??null,n=p.map(a=>`
        <option value="${a.id}" ${a.id===v?"selected":""}>
            ${u(a.name)}
        </option>
    `).join("")}async function j(e,t){if(!e||!c)return;let n=++E;p=[],v=null,q.textContent=t,J();try{let a=await y(e);if(n!==E)return;let o=await L(a.occupationId);if(n!==E)return;p=o.accounts,v=p.find(r=>r.status==="abierta")?.id??p[0]?.id??null,w.classList.add("bg-emerald-50","text-emerald-700"),w.textContent="En atenci\xF3n",K(a)}catch(a){if(n!==E)return;let o=a instanceof Error?a.message:"No fue posible consultar la mesa.";w.classList.add("bg-red-50","text-red-700"),w.textContent=`${u(o)}`}}function B(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),a=document.getElementById("orderWorkspaceTitle");!e||!t||!n||!a||(Q(),c=e,O=t,G=n,q=a,c.inert=!0,c.addEventListener("click",o=>{o.target.closest("[data-close-order-workspace]")&&h()}),c.addEventListener("change",o=>{let r=o.target;if(!r.matches("[data-order-workspace-account]"))return;v=Number(r.value);let i=p.find(d=>d.id===v),s=c.querySelector("[data-order-workspace-account-info]");i&&s&&(s.textContent=`Estado: ${i.status} \xB7 ${i.invoiceId===null?"Sin orden todav\xEDa":`Orden #${i.invoiceId}`}`)}),O.addEventListener("click",h),document.addEventListener("keydown",o=>{o.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&h()}),c.querySelector("#catalogLoadMore")?.addEventListener("click",()=>{catalogLoading||!catalogHasMore||C(catalogCategoryId,!0)}),C())}async function Q(){let e=await S(),t=document.querySelector("#divCategorias");t.innerHTML=`
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
                data-categoria-id="${u(n.id)}"
                aria-pressed="false"
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${u(n.nombre)}
            </button>
        `).join("")}
    `,t.addEventListener("click",n=>{let a=n.target.closest("button[data-categoria-id]");if(!a||!t.contains(a))return;let o=a.dataset.categoriaId,r=o==="all"?null:Number(o);r!==null&&(!Number.isInteger(r)||r<=0)||(t.querySelectorAll("button[data-categoria-id]").forEach(i=>{let s=i===a;i.setAttribute("aria-pressed",String(s)),i.classList.toggle("border-indigo-600",s),i.classList.toggle("bg-indigo-600",s),i.classList.toggle("text-white",s),i.classList.toggle("border-slate-200",!s),i.classList.toggle("bg-white",!s),i.classList.toggle("text-slate-600",!s),i.classList.toggle("hover:bg-slate-50",!s)}),C(r))})}async function C(e=null,t=!1){let n=document.querySelector("#catalogoPaginador"),a=document.querySelector("#catalogProductGrid"),o=document.querySelector("#catalogProductCount"),r=document.querySelector("#catalogProductStatus");if(!a||!o||!r)return;let i=++N;a.replaceChildren(),o.textContent="Cargando...",r.textContent="Cargando productos...";try{let s=await D(1,e);if(i!==N)return;o.textContent=`${s.products.length} en esta p\xE1gina`,r.textContent=s.products.length>0?`Mostrando p\xE1gina ${s.page}`:"No hay productos vendibles en esta sucursal.",a.innerHTML=s.products.map(d=>{let T=u(d.nombre),M=d.foto&&d.foto!=="null"&&d.foto!=="undefined"?d.foto:null,W=Number(d.precioVenta).toLocaleString();return`
                <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${M!=null?"":"grid place-items-center"}">
                        ${M!=null?`<img src="/build/img/${u(M)}" alt="${T}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${T}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${u(W)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${T}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("")}catch(s){o.textContent="Sin cargar",r.textContent=s instanceof Error?s.message:"No fue posible cargar los productos."}}function de(){k(),B(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),a=n?.closest("[data-table-id]");if(!n||!a)return;let o=Number(a.dataset.resourceId);o&&(n.hasAttribute("data-open-active-detail")?(h(),P(o)):(b(),j(o,a.dataset.tableName??"Mesa")))})}export{de as initTables};
//# sourceMappingURL=tables-M4FKGE7H.js.map
