async function h(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),a=await n.json();if(!n.ok||!a.success||!a.occupation)throw new Error(a.message??"No fue posible consultar la atenci\xF3n.");return a.occupation}async function y(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var l,A,g,C,I;function R(){window.clearTimeout(I),A.classList.remove("hidden"),l.inert=!1,requestAnimationFrame(()=>{l.classList.remove("translate-x-full")}),l.setAttribute("aria-hidden","false")}function b(){l.getAttribute("aria-hidden")!=="true"&&(++m,l.classList.add("translate-x-full"),l.setAttribute("aria-hidden","true"),l.inert=!0,I=window.setTimeout(()=>{A.classList.add("hidden")},300))}function W(){g.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function v(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function B(e){let t=e.clientName?v(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",a=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",r=e.observations?v(e.observations):"Sin observaciones";g.innerHTML=`
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
                ${p("Inicio",T(e.startDate))}
                ${p("Fin estimado",e.estimatedEndDate?T(e.estimatedEndDate):"\u2014")}
                ${p("Observaciones",r)}
            </div>
        </div>`}function p(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function T(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function _(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${v(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${v(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,g.appendChild(t)}function V(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,g.appendChild(t)}var m=0;function M(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),a=document.getElementById("activeOccupationActions");!e||!t||!n||!a||(l=e,A=t,g=n,C=a,l.inert=!0,l.addEventListener("click",r=>{r.target.closest("[data-close-active-occupation]")&&b()}),A.addEventListener("click",b),document.addEventListener("keydown",r=>{r.key==="Escape"&&l.getAttribute("aria-hidden")==="false"&&b()}))}async function $(e){if(!e)return;let t=++m;R(),W(),C.classList.add("hidden");try{let n=await h(e);if(t!==m)return;B(n);try{let a=await y(n.occupationId);if(t!==m)return;_(a.accounts)}catch(a){if(t!==m)return;V(a instanceof Error?a.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==m)return;g.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${v(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function d(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}async function H(){let t=await fetch("/admin/api/inventory/getAllcategorias");if(!t.ok)throw new Error("No fue posible consultar las categorias.");let n=await t.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}async function k(e=1,t=null){let n=new URLSearchParams({page:String(e)});t!=null&&n.set("categoriaId",String(t));let a=`/admin/api/inventory/listSimpleProducts?${n.toString()}`,r=await fetch(a),o=await r.json();if(!r.ok||!o.success||!o.data||!Array.isArray(o.data.products))throw new Error("No fue posible consultar las productos.");return o.data}var c,w,F,P,x=0;var S,u=[],f=null,L=document.querySelector("#atencionMesaText");function z(){window.clearTimeout(S),w.classList.remove("hidden"),c.inert=!1,c.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>c.classList.remove("translate-x-full"))}function E(){if(c.getAttribute("aria-hidden")==="true")return;++x;let e=document.activeElement;e instanceof HTMLElement&&c.contains(e)&&e.blur(),c.classList.add("translate-x-full"),c.inert=!0,c.setAttribute("aria-hidden","true"),S=window.setTimeout(()=>w.classList.add("hidden"),300)}function U(e){let t=u.find(a=>a.id===f)??null,n=u.map(a=>`
        <option value="${a.id}" ${a.id===f?"selected":""}>
            ${d(a.name)}
        </option>
    `).join("")}async function N(e,t){if(!e||!c)return;let n=++x;u=[],f=null,P.textContent=t,z();try{let a=await h(e);if(n!==x)return;let r=await y(a.occupationId);if(n!==x)return;u=r.accounts,f=u.find(o=>o.status==="abierta")?.id??u[0]?.id??null,L.classList.add("bg-emerald-50","text-emerald-700"),L.textContent="En atenci\xF3n",U(a)}catch(a){if(n!==x)return;let r=a instanceof Error?a.message:"No fue posible consultar la mesa.";L.classList.add("bg-red-50","text-red-700"),L.textContent=`${d(r)}`}}function D(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),a=document.getElementById("orderWorkspaceTitle");!e||!t||!n||!a||(G(),c=e,w=t,F=n,P=a,c.inert=!0,c.addEventListener("click",r=>{r.target.closest("[data-close-order-workspace]")&&E()}),c.addEventListener("change",r=>{let o=r.target;if(!o.matches("[data-order-workspace-account]"))return;f=Number(o.value);let s=u.find(O=>O.id===f),i=c.querySelector("[data-order-workspace-account-info]");s&&i&&(i.textContent=`Estado: ${s.status} \xB7 ${s.invoiceId===null?"Sin orden todav\xEDa":`Orden #${s.invoiceId}`}`)}),w.addEventListener("click",E),document.addEventListener("keydown",r=>{r.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&E()}),q())}async function G(){let e=await H(),t=document.querySelector("#divCategorias");t.innerHTML=`
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
    `,t.addEventListener("click",n=>{let a=n.target.closest("button[data-categoria-id]");if(!a||!t.contains(a))return;let r=a.dataset.categoriaId,o=r==="all"?null:Number(r);o!==null&&(!Number.isInteger(o)||o<=0)||(t.querySelectorAll("button[data-categoria-id]").forEach(s=>{let i=s===a;s.setAttribute("aria-pressed",String(i)),s.classList.toggle("border-indigo-600",i),s.classList.toggle("bg-indigo-600",i),s.classList.toggle("text-white",i),s.classList.toggle("border-slate-200",!i),s.classList.toggle("bg-white",!i),s.classList.toggle("text-slate-600",!i),s.classList.toggle("hover:bg-slate-50",!i)}),q(o))})}async function q(e=null){let t=document.querySelector("#catalogProductGrid"),n=document.querySelector("#catalogProductCount"),a=document.querySelector("#catalogProductStatus");if(!(!t||!n||!a)){t.replaceChildren(),n.textContent="Cargando...",a.textContent="Cargando productos...";try{let r=await k(1,e);n.textContent=`${r.products.length} en esta p\xE1gina`,a.textContent=r.products.length>0?`Mostrando p\xE1gina ${r.page}`:"No hay productos vendibles en esta sucursal.",t.innerHTML=r.products.map(o=>{let s=d(o.nombre),i=o.foto&&o.foto!=="null"&&o.foto!=="undefined"?o.foto:null,j=Number(o.precioVenta).toLocaleString();return`
                <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${i!=null?"":"grid place-items-center"}">
                        ${i!=null?`<img src="/build/img/${d(i)}" alt="${s}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${s}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${d(j)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${s}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("")}catch(r){n.textContent="Sin cargar",a.textContent=r instanceof Error?r.message:"No fue posible cargar los productos."}}}function se(){M(),D(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),a=n?.closest("[data-table-id]");if(!n||!a)return;let r=Number(a.dataset.resourceId);r&&(n.hasAttribute("data-open-active-detail")?(E(),$(r)):(b(),N(r,a.dataset.tableName??"Mesa")))})}export{se as initTables};
//# sourceMappingURL=tables-QJUTKBAD.js.map
