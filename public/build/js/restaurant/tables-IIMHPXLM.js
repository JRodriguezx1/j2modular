async function E(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),i=await n.json();if(!n.ok||!i.success||!i.occupation)throw new Error(i.message??"No fue posible consultar la atenci\xF3n.");return i.occupation}async function O(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var c,x,l,w,h;function N(){window.clearTimeout(h),x.classList.remove("hidden"),c.inert=!1,requestAnimationFrame(()=>{c.classList.remove("translate-x-full")}),c.setAttribute("aria-hidden","false")}function p(){c.getAttribute("aria-hidden")!=="true"&&(++d,c.classList.add("translate-x-full"),c.setAttribute("aria-hidden","true"),c.inert=!0,h=window.setTimeout(()=>{x.classList.add("hidden")},300))}function C(){l.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function f(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function W(e){let t=e.clientName?f(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",i=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",a=e.observations?f(e.observations):"Sin observaciones";l.innerHTML=`
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
                ${s("Cliente",t)}
                ${s("Personas",n)}
                ${s("Origen",i)}
                ${s("Inicio",L(e.startDate))}
                ${s("Fin estimado",e.estimatedEndDate?L(e.estimatedEndDate):"\u2014")}
                ${s("Observaciones",a)}
            </div>
        </div>`}function s(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function L(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function S(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${f(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${f(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,l.appendChild(t)}function R(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,l.appendChild(t)}var d=0;function T(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),i=document.getElementById("activeOccupationActions");!e||!t||!n||!i||(c=e,x=t,l=n,w=i,c.inert=!0,c.addEventListener("click",a=>{a.target.closest("[data-close-active-occupation]")&&p()}),x.addEventListener("click",p),document.addEventListener("keydown",a=>{a.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&p()}))}async function I(e){if(!e)return;let t=++d;N(),C(),w.classList.add("hidden");try{let n=await E(e);if(t!==d)return;W(n);try{let i=await O(n.occupationId);if(t!==d)return;S(i.accounts)}catch(i){if(t!==d)return;R(i instanceof Error?i.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==d)return;l.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${f(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}var r,A,B,k,v=0,H,o=[],u=null;function P(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function j(){window.clearTimeout(H),A.classList.remove("hidden"),r.inert=!1,requestAnimationFrame(()=>r.classList.remove("translate-x-full")),r.setAttribute("aria-hidden","false")}function g(){r.getAttribute("aria-hidden")!=="true"&&(++v,r.classList.add("translate-x-full"),r.setAttribute("aria-hidden","true"),r.inert=!0,H=window.setTimeout(()=>A.classList.add("hidden"),300))}function q(e){let t=o.find(i=>i.id===u)??null,n=o.map(i=>`
        <option value="${i.id}" ${i.id===u?"selected":""}>
            ${P(i.name)}
        </option>
    `).join("")}async function $(e,t){if(!e||!r)return;let n=++v;o=[],u=null,k.textContent=t,j();try{let i=await E(e);if(n!==v)return;let a=await O(i.occupationId);if(n!==v)return;o=a.accounts,u=o.find(m=>m.status==="abierta")?.id??o[0]?.id??null,q(i)}catch(i){if(n!==v)return;let a=i instanceof Error?i.message:"No fue posible consultar la mesa."}}function M(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),i=document.getElementById("orderWorkspaceTitle");!e||!t||!n||!i||(r=e,A=t,B=n,k=i,r.inert=!0,r.addEventListener("click",a=>{a.target.closest("[data-close-order-workspace]")&&g()}),r.addEventListener("change",a=>{let m=a.target;if(!m.matches("[data-order-workspace-account]"))return;u=Number(m.value);let b=o.find(D=>D.id===u),y=r.querySelector("[data-order-workspace-account-info]");b&&y&&(y.textContent=`Estado: ${b.status} \xB7 ${b.invoiceId===null?"Sin orden todav\xEDa":`Orden #${b.invoiceId}`}`)}),A.addEventListener("click",g),document.addEventListener("keydown",a=>{a.key==="Escape"&&r.getAttribute("aria-hidden")==="false"&&g()}))}function K(){T(),M(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),i=n?.closest("[data-table-id]");if(!n||!i)return;let a=Number(i.dataset.resourceId);a&&(n.hasAttribute("data-open-active-detail")?(g(),I(a)):(p(),$(a,i.dataset.tableName??"Mesa")))})}export{K as initTables};
//# sourceMappingURL=tables-IIMHPXLM.js.map
