async function x(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),a=await n.json();if(!n.ok||!a.success||!a.occupation)throw new Error(a.message??"No fue posible consultar la atenci\xF3n.");return a.occupation}async function E(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var o,A,d,w,T;function W(){window.clearTimeout(T),A.classList.remove("hidden"),o.inert=!1,requestAnimationFrame(()=>{o.classList.remove("translate-x-full")}),o.setAttribute("aria-hidden","false")}function p(){o.getAttribute("aria-hidden")!=="true"&&(++l,o.classList.add("translate-x-full"),o.setAttribute("aria-hidden","true"),o.inert=!0,T=window.setTimeout(()=>{A.classList.add("hidden")},300))}function q(){d.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function f(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function P(e){let t=e.clientName?f(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",a=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",i=e.observations?f(e.observations):"Sin observaciones";d.innerHTML=`
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
                ${c("Origen",a)}
                ${c("Inicio",h(e.startDate))}
                ${c("Fin estimado",e.estimatedEndDate?h(e.estimatedEndDate):"\u2014")}
                ${c("Observaciones",i)}
            </div>
        </div>`}function c(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function h(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function R(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${f(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${f(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,d.appendChild(t)}function j(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,d.appendChild(t)}var l=0;function I(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),a=document.getElementById("activeOccupationActions");!e||!t||!n||!a||(o=e,A=t,d=n,w=a,o.inert=!0,o.addEventListener("click",i=>{i.target.closest("[data-close-active-occupation]")&&p()}),A.addEventListener("click",p),document.addEventListener("keydown",i=>{i.key==="Escape"&&o.getAttribute("aria-hidden")==="false"&&p()}))}async function k(e){if(!e)return;let t=++l;W(),q(),w.classList.add("hidden");try{let n=await x(e);if(t!==l)return;P(n);try{let a=await E(n.occupationId);if(t!==l)return;R(a.accounts)}catch(a){if(t!==l)return;j(a instanceof Error?a.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==l)return;d.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${f(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}async function M(){return await(await fetch("/admin/api/inventory/getAllcategorias")).json()}var r,y,B,H,v=0,C,s=[],u=null,O=document.querySelector("#atencionMesaText");function $(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function _(){window.clearTimeout(C),y.classList.remove("hidden"),r.inert=!1,r.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>r.classList.remove("translate-x-full"))}function g(){if(r.getAttribute("aria-hidden")==="true")return;++v;let e=document.activeElement;e instanceof HTMLElement&&r.contains(e)&&e.blur(),r.classList.add("translate-x-full"),r.inert=!0,r.setAttribute("aria-hidden","true"),C=window.setTimeout(()=>y.classList.add("hidden"),300)}function F(e){let t=s.find(a=>a.id===u)??null,n=s.map(a=>`
        <option value="${a.id}" ${a.id===u?"selected":""}>
            ${$(a.name)}
        </option>
    `).join("")}async function D(e,t){if(!e||!r)return;let n=++v;s=[],u=null,H.textContent=t,_();try{let a=await x(e);if(n!==v)return;let i=await E(a.occupationId);if(n!==v)return;s=i.accounts,u=s.find(m=>m.status==="abierta")?.id??s[0]?.id??null,O.classList.add("bg-emerald-50","text-emerald-700"),O.textContent="En atenci\xF3n",F(a)}catch(a){if(n!==v)return;let i=a instanceof Error?a.message:"No fue posible consultar la mesa.";O.classList.add("bg-red-50","text-red-700"),O.textContent=`${$(i)}`}}function N(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),a=document.getElementById("orderWorkspaceTitle");!e||!t||!n||!a||(U(),r=e,y=t,B=n,H=a,r.inert=!0,r.addEventListener("click",i=>{i.target.closest("[data-close-order-workspace]")&&g()}),r.addEventListener("change",i=>{let m=i.target;if(!m.matches("[data-order-workspace-account]"))return;u=Number(m.value);let b=s.find(S=>S.id===u),L=r.querySelector("[data-order-workspace-account-info]");b&&L&&(L.textContent=`Estado: ${b.status} \xB7 ${b.invoiceId===null?"Sin orden todav\xEDa":`Orden #${b.invoiceId}`}`)}),y.addEventListener("click",g),document.addEventListener("keydown",i=>{i.key==="Escape"&&r.getAttribute("aria-hidden")==="false"&&g()}))}async function U(){let e=await M(),t=document.querySelector("#divCategorias"),n="";e.forEach(a=>{n+=`
            <button 
                type="button" 
                class="shrink-0 rounded-lg border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600 hover:bg-slate-50">
                ${a.nombre}
            </button>`}),t.innerHTML=n,console.log(n)}function te(){I(),N(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),a=n?.closest("[data-table-id]");if(!n||!a)return;let i=Number(a.dataset.resourceId);i&&(n.hasAttribute("data-open-active-detail")?(g(),k(i)):(p(),D(i,a.dataset.tableName??"Mesa")))})}export{te as initTables};
//# sourceMappingURL=tables-RBQJBZQ5.js.map
