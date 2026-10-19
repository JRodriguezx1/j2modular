async function h(t){let e=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(t)}`,n=await fetch(e,{headers:{Accept:"application/json"}}),a=await n.json();if(!n.ok||!a.success||!a.occupation)throw new Error(a.message??"No fue posible consultar la atenci\xF3n.");return a.occupation}async function y(t){let e=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(t)}`,{headers:{Accept:"application/json"}}),n=await e.json();if(!e.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var l,A,g,I,C;function R(){window.clearTimeout(C),A.classList.remove("hidden"),l.inert=!1,requestAnimationFrame(()=>{l.classList.remove("translate-x-full")}),l.setAttribute("aria-hidden","false")}function b(){l.getAttribute("aria-hidden")!=="true"&&(++m,l.classList.add("translate-x-full"),l.setAttribute("aria-hidden","true"),l.inert=!0,C=window.setTimeout(()=>{A.classList.add("hidden")},300))}function W(){g.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function v(t){let e=document.createElement("div");return e.textContent=t,e.innerHTML}function B(t){let e=t.clientName?v(t.clientName):"Sin cliente asociado",n=t.numberOfPeople!==null?String(t.numberOfPeople):"\u2014",a=t.reservationId!==null?`#${t.reservationId}`:"Atenci\xF3n directa",r=t.observations?v(t.observations):"Sin observaciones";g.innerHTML=`
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
                ${p("Cliente",e)}
                ${p("Personas",n)}
                ${p("Origen",a)}
                ${p("Inicio",T(t.startDate))}
                ${p("Fin estimado",t.estimatedEndDate?T(t.estimatedEndDate):"\u2014")}
                ${p("Observaciones",r)}
            </div>
        </div>`}function p(t,e){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${t}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${e}
            </p>
        </div>`}function T(t){let e=new Date(t.replace(" ","T"));return Number.isNaN(e.getTime())?t:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(e)}function _(t){let e=document.createElement("section");e.className="mt-8 border-t border-slate-200 pt-5",e.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${t.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':t.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${v(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${v(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,g.appendChild(e)}function F(t){let e=document.createElement("p");e.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",e.textContent=t,g.appendChild(e)}var m=0;function M(){let t=document.getElementById("activeOccupationDrawer"),e=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),a=document.getElementById("activeOccupationActions");!t||!e||!n||!a||(l=t,A=e,g=n,I=a,l.inert=!0,l.addEventListener("click",r=>{r.target.closest("[data-close-active-occupation]")&&b()}),A.addEventListener("click",b),document.addEventListener("keydown",r=>{r.key==="Escape"&&l.getAttribute("aria-hidden")==="false"&&b()}))}async function $(t){if(!t)return;let e=++m;R(),W(),I.classList.add("hidden");try{let n=await h(t);if(e!==m)return;B(n);try{let a=await y(n.occupationId);if(e!==m)return;_(a.accounts)}catch(a){if(e!==m)return;F(a instanceof Error?a.message:"No fue posible consultar las cuentas.")}}catch(n){if(e!==m)return;g.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${v(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function d(t){let e=document.createElement("div");return e.textContent=t,e.innerHTML}async function H(){let e=await fetch("/admin/api/inventory/getAllcategorias");if(!e.ok)throw new Error("No fue posible consultar las categorias.");let n=await e.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}async function k(t=1,e=null){let n=new URLSearchParams({page:String(t)});e!=null&&n.set("categoriaId",String(e));let a=`/admin/api/inventory/listSimpleProducts?${n.toString()}`,r=await fetch(a),o=await r.json();if(!r.ok||!o.success||!o.data||!Array.isArray(o.data.products))throw new Error("No fue posible consultar las productos.");return o.data}var c,w,V,P,x=0,S,u=[],f=null,L=document.querySelector("#atencionMesaText");function z(){window.clearTimeout(S),w.classList.remove("hidden"),c.inert=!1,c.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>c.classList.remove("translate-x-full"))}function E(){if(c.getAttribute("aria-hidden")==="true")return;++x;let t=document.activeElement;t instanceof HTMLElement&&c.contains(t)&&t.blur(),c.classList.add("translate-x-full"),c.inert=!0,c.setAttribute("aria-hidden","true"),S=window.setTimeout(()=>w.classList.add("hidden"),300)}function U(t){let e=u.find(a=>a.id===f)??null,n=u.map(a=>`
        <option value="${a.id}" ${a.id===f?"selected":""}>
            ${d(a.name)}
        </option>
    `).join("")}async function N(t,e){if(!t||!c)return;let n=++x;u=[],f=null,P.textContent=e,z();try{let a=await h(t);if(n!==x)return;let r=await y(a.occupationId);if(n!==x)return;u=r.accounts,f=u.find(o=>o.status==="abierta")?.id??u[0]?.id??null,L.classList.add("bg-emerald-50","text-emerald-700"),L.textContent="En atenci\xF3n",U(a)}catch(a){if(n!==x)return;let r=a instanceof Error?a.message:"No fue posible consultar la mesa.";L.classList.add("bg-red-50","text-red-700"),L.textContent=`${d(r)}`}}function D(){let t=document.getElementById("orderWorkspaceDrawer"),e=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),a=document.getElementById("orderWorkspaceTitle");!t||!e||!n||!a||(G(),c=t,w=e,V=n,P=a,c.inert=!0,c.addEventListener("click",r=>{r.target.closest("[data-close-order-workspace]")&&E()}),c.addEventListener("change",r=>{let o=r.target;if(!o.matches("[data-order-workspace-account]"))return;f=Number(o.value);let s=u.find(O=>O.id===f),i=c.querySelector("[data-order-workspace-account-info]");s&&i&&(i.textContent=`Estado: ${s.status} \xB7 ${s.invoiceId===null?"Sin orden todav\xEDa":`Orden #${s.invoiceId}`}`)}),w.addEventListener("click",E),document.addEventListener("keydown",r=>{r.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&E()}),j())}async function G(){let t=await H(),e=document.querySelector("#divCategorias");e.innerHTML=`
        <button
            type="button"
            data-categoria-id="all"
            aria-pressed="true"
            class="btncateogira shrink-0 rounded-lg border border-indigo-600 bg-indigo-600 px-3 py-2 font-medium text-white">
            Todas
        </button>
        ${t.map(n=>`
            <button
                type="button"
                data-categoria-id="${d(n.id)}"
                aria-pressed="false"
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${d(n.nombre)}
            </button>
        `).join("")}
    `,e.addEventListener("click",n=>{let a=n.target.closest("button[data-categoria-id]");if(!a||!e.contains(a))return;let r=a.dataset.categoriaId,o=r==="all"?null:Number(r);o!==null&&(!Number.isInteger(o)||o<=0)||(e.querySelectorAll("button[data-categoria-id]").forEach(s=>{let i=s===a;s.setAttribute("aria-pressed",String(i)),s.classList.toggle("border-indigo-600",i),s.classList.toggle("bg-indigo-600",i),s.classList.toggle("text-white",i),s.classList.toggle("border-slate-200",!i),s.classList.toggle("bg-white",!i),s.classList.toggle("text-slate-600",!i),s.classList.toggle("hover:bg-slate-50",!i)}),j(o))})}async function j(t=null){let e=document.querySelector("#catalogProductGrid"),n=document.querySelector("#catalogProductCount"),a=document.querySelector("#catalogProductStatus");if(!(!e||!n||!a)){e.replaceChildren(),n.textContent="Cargando...",a.textContent="Cargando productos...";try{let r=await k();n.textContent=`${r.products.length} en esta p\xE1gina`,a.textContent=r.products.length>0?`Mostrando p\xE1gina ${r.page}`:"No hay productos vendibles en esta sucursal.",e.innerHTML=r.products.map(o=>{let s=d(o.nombre),i=o.foto&&o.foto!=="null"&&o.foto!=="undefined"?o.foto:null,q=Number(o.precioVenta).toLocaleString();return`
                <article class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-indigo-300 hover:shadow-md">
                    <div class="h-32 bg-slate-100 ${i!=null?"":"grid place-items-center"}">
                        ${i!=null?`<img src="/build/img/${d(i)}" alt="${s}" loading="lazy" class="h-full w-full object-cover">`:'<span class=" material-symbols-outlined text-5xl">restaurant</span>'}
                    </div>
                    <div class="p-3">
                        <h4 class="text-base font-semibold text-slate-900">${s}</h4>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-lg font-bold text-slate-900">$${d(q)}</span>
                            <button
                                type="button"
                                aria-label="Agregar ${s}"
                                class="flex size-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white"
                            >
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                </article>`}).join("")}catch(r){n.textContent="Sin cargar",a.textContent=r instanceof Error?r.message:"No fue posible cargar los productos."}}}function se(){M(),D(),document.addEventListener("click",t=>{let n=t.target.closest("[data-open-active-detail], [data-open-order-workspace]"),a=n?.closest("[data-table-id]");if(!n||!a)return;let r=Number(a.dataset.resourceId);r&&(n.hasAttribute("data-open-active-detail")?(E(),$(r)):(b(),N(r,a.dataset.tableName??"Mesa")))})}export{se as initTables};
//# sourceMappingURL=tables-X2IYZJ6G.js.map
