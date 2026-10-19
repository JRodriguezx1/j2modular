async function x(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),r=await n.json();if(!n.ok||!r.success||!r.occupation)throw new Error(r.message??"No fue posible consultar la atenci\xF3n.");return r.occupation}async function E(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var i,y,d,T,C;function N(){window.clearTimeout(C),y.classList.remove("hidden"),i.inert=!1,requestAnimationFrame(()=>{i.classList.remove("translate-x-full")}),i.setAttribute("aria-hidden","false")}function p(){i.getAttribute("aria-hidden")!=="true"&&(++l,i.classList.add("translate-x-full"),i.setAttribute("aria-hidden","true"),i.inert=!0,C=window.setTimeout(()=>{y.classList.add("hidden")},300))}function q(){d.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function g(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function R(e){let t=e.clientName?g(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",r=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",o=e.observations?g(e.observations):"Sin observaciones";d.innerHTML=`
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
                ${c("Origen",r)}
                ${c("Inicio",L(e.startDate))}
                ${c("Fin estimado",e.estimatedEndDate?L(e.estimatedEndDate):"\u2014")}
                ${c("Observaciones",o)}
            </div>
        </div>`}function c(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function L(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function j(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${g(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${g(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,d.appendChild(t)}function W(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,d.appendChild(t)}var l=0;function I(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),r=document.getElementById("activeOccupationActions");!e||!t||!n||!r||(i=e,y=t,d=n,T=r,i.inert=!0,i.addEventListener("click",o=>{o.target.closest("[data-close-active-occupation]")&&p()}),y.addEventListener("click",p),document.addEventListener("keydown",o=>{o.key==="Escape"&&i.getAttribute("aria-hidden")==="false"&&p()}))}async function M(e){if(!e)return;let t=++l;N(),q(),T.classList.add("hidden");try{let n=await x(e);if(t!==l)return;R(n);try{let r=await E(n.occupationId);if(t!==l)return;j(r.accounts)}catch(r){if(t!==l)return;W(r instanceof Error?r.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==l)return;d.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${g(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function A(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}async function P(){let t=await fetch("/admin/api/inventory/getAllcategorias");if(!t.ok)throw new Error("No fue posible consultar las categorias.");let n=await t.json();if(!Array.isArray(n))throw new Error("La respuesta de categor\xEDas no tiene el formato esperado.");return n}var a,h,B,H,f=0,k,s=[],u=null,O=document.querySelector("#atencionMesaText");function F(){window.clearTimeout(k),h.classList.remove("hidden"),a.inert=!1,a.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>a.classList.remove("translate-x-full"))}function v(){if(a.getAttribute("aria-hidden")==="true")return;++f;let e=document.activeElement;e instanceof HTMLElement&&a.contains(e)&&e.blur(),a.classList.add("translate-x-full"),a.inert=!0,a.setAttribute("aria-hidden","true"),k=window.setTimeout(()=>h.classList.add("hidden"),300)}function _(e){let t=s.find(r=>r.id===u)??null,n=s.map(r=>`
        <option value="${r.id}" ${r.id===u?"selected":""}>
            ${A(r.name)}
        </option>
    `).join("")}async function $(e,t){if(!e||!a)return;let n=++f;s=[],u=null,H.textContent=t,F();try{let r=await x(e);if(n!==f)return;let o=await E(r.occupationId);if(n!==f)return;s=o.accounts,u=s.find(m=>m.status==="abierta")?.id??s[0]?.id??null,O.classList.add("bg-emerald-50","text-emerald-700"),O.textContent="En atenci\xF3n",_(r)}catch(r){if(n!==f)return;let o=r instanceof Error?r.message:"No fue posible consultar la mesa.";O.classList.add("bg-red-50","text-red-700"),O.textContent=`${A(o)}`}}function S(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),r=document.getElementById("orderWorkspaceTitle");!e||!t||!n||!r||(V(),a=e,h=t,B=n,H=r,a.inert=!0,a.addEventListener("click",o=>{o.target.closest("[data-close-order-workspace]")&&v()}),a.addEventListener("change",o=>{let m=o.target;if(!m.matches("[data-order-workspace-account]"))return;u=Number(m.value);let b=s.find(D=>D.id===u),w=a.querySelector("[data-order-workspace-account-info]");b&&w&&(w.textContent=`Estado: ${b.status} \xB7 ${b.invoiceId===null?"Sin orden todav\xEDa":`Orden #${b.invoiceId}`}`)}),h.addEventListener("click",v),document.addEventListener("keydown",o=>{o.key==="Escape"&&a.getAttribute("aria-hidden")==="false"&&v()}))}async function V(){let e=await P(),t=document.querySelector("#divCategorias"),n="";e.forEach(r=>{n+=`
            <button
                id="idCategoria_${r.id}"
                type="button" 
                class="btncateogira shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${A(r.nombre)}
            </button>`}),t.innerHTML=n}function oe(){I(),S(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),r=n?.closest("[data-table-id]");if(!n||!r)return;let o=Number(r.dataset.resourceId);o&&(n.hasAttribute("data-open-active-detail")?(v(),M(o)):(p(),$(o,r.dataset.tableName??"Mesa")))})}export{oe as initTables};
//# sourceMappingURL=tables-6TC42NEZ.js.map
