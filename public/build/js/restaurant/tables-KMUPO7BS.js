async function x(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),i=await n.json();if(!n.ok||!i.success||!i.occupation)throw new Error(i.message??"No fue posible consultar la atenci\xF3n.");return i.occupation}async function E(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var s,A,u,T,k;function S(){window.clearTimeout(k),A.classList.remove("hidden"),s.inert=!1,requestAnimationFrame(()=>{s.classList.remove("translate-x-full")}),s.setAttribute("aria-hidden","false")}function p(){s.getAttribute("aria-hidden")!=="true"&&(++d,s.classList.add("translate-x-full"),s.setAttribute("aria-hidden","true"),s.inert=!0,k=window.setTimeout(()=>{A.classList.add("hidden")},300))}function P(){u.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function g(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function W(e){let t=e.clientName?g(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",i=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",r=e.observations?g(e.observations):"Sin observaciones";u.innerHTML=`
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
                ${c("Cliente",t)}
                ${c("Personas",n)}
                ${c("Origen",i)}
                ${c("Inicio",L(e.startDate))}
                ${c("Fin estimado",e.estimatedEndDate?L(e.estimatedEndDate):"\u2014")}
                ${c("Observaciones",r)}
            </div>
        </div>`}function c(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function L(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function q(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${g(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${g(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,u.appendChild(t)}function R(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,u.appendChild(t)}var d=0;function I(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),i=document.getElementById("activeOccupationActions");!e||!t||!n||!i||(s=e,A=t,u=n,T=i,s.inert=!0,s.addEventListener("click",r=>{r.target.closest("[data-close-active-occupation]")&&p()}),A.addEventListener("click",p),document.addEventListener("keydown",r=>{r.key==="Escape"&&s.getAttribute("aria-hidden")==="false"&&p()}))}async function C(e){if(!e)return;let t=++d;S(),P(),T.classList.add("hidden");try{let n=await x(e);if(t!==d)return;W(n);try{let i=await E(n.occupationId);if(t!==d)return;q(i.accounts)}catch(i){if(t!==d)return;R(i instanceof Error?i.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==d)return;u.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${g(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function O(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}async function M(){let t=await fetch("/admin/api/inventory/getAllcategorias");if(!t.ok)throw new Error("No fue posible consultar las categorias.");let n=await t.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}var o,y,j,H,f=0,$,a=[],l=null,w=document.querySelector("#atencionMesaText");function B(){window.clearTimeout($),y.classList.remove("hidden"),o.inert=!1,o.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>o.classList.remove("translate-x-full"))}function v(){if(o.getAttribute("aria-hidden")==="true")return;++f;let e=document.activeElement;e instanceof HTMLElement&&o.contains(e)&&e.blur(),o.classList.add("translate-x-full"),o.inert=!0,o.setAttribute("aria-hidden","true"),$=window.setTimeout(()=>y.classList.add("hidden"),300)}function F(e){let t=a.find(i=>i.id===l)??null,n=a.map(i=>`
        <option value="${i.id}" ${i.id===l?"selected":""}>
            ${O(i.name)}
        </option>
    `).join("")}async function _(e,t){if(!e||!o)return;let n=++f;a=[],l=null,H.textContent=t,B();try{let i=await x(e);if(n!==f)return;let r=await E(i.occupationId);if(n!==f)return;a=r.accounts,l=a.find(m=>m.status==="abierta")?.id??a[0]?.id??null,w.classList.add("bg-emerald-50","text-emerald-700"),w.textContent="En atenci\xF3n",F(i)}catch(i){if(n!==f)return;let r=i instanceof Error?i.message:"No fue posible consultar la mesa.";w.classList.add("bg-red-50","text-red-700"),w.textContent=`${O(r)}`}}function D(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),i=document.getElementById("orderWorkspaceTitle");!e||!t||!n||!i||(U(),o=e,y=t,j=n,H=i,o.inert=!0,o.addEventListener("click",r=>{r.target.closest("[data-close-order-workspace]")&&v()}),o.addEventListener("change",r=>{let m=r.target;if(!m.matches("[data-order-workspace-account]"))return;l=Number(m.value);let b=a.find(N=>N.id===l),h=o.querySelector("[data-order-workspace-account-info]");b&&h&&(h.textContent=`Estado: ${b.status} \xB7 ${b.invoiceId===null?"Sin orden todav\xEDa":`Orden #${b.invoiceId}`}`)}),y.addEventListener("click",v),document.addEventListener("keydown",r=>{r.key==="Escape"&&o.getAttribute("aria-hidden")==="false"&&v()}))}async function U(){let e=await M(),t=document.querySelector("#divCategorias"),n="";e.forEach(i=>{n+=`
            <button
                id="idCategoria_${i.id}"
                type="button" 
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${O(i.nombre)}
            </button>`}),t.innerHTML=n}function ie(){I(),D(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),i=n?.closest("[data-table-id]");if(!n||!i)return;let r=Number(i.dataset.resourceId);r&&(n.hasAttribute("data-open-active-detail")?(v(),C(r)):(p(),_(r,i.dataset.tableName??"Mesa")))})}export{ie as initTables};
//# sourceMappingURL=tables-KMUPO7BS.js.map
