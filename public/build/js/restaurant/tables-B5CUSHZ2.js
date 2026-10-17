async function x(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),i=await n.json();if(!n.ok||!i.success||!i.occupation)throw new Error(i.message??"No fue posible consultar la atenci\xF3n.");return i.occupation}async function E(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var c,O,l,w,h;function W(){window.clearTimeout(h),O.classList.remove("hidden"),c.inert=!1,requestAnimationFrame(()=>{c.classList.remove("translate-x-full")}),c.setAttribute("aria-hidden","false")}function p(){c.getAttribute("aria-hidden")!=="true"&&(++d,c.classList.add("translate-x-full"),c.setAttribute("aria-hidden","true"),c.inert=!0,h=window.setTimeout(()=>{O.classList.add("hidden")},300))}function S(){l.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function f(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function R(e){let t=e.clientName?f(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",i=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",r=e.observations?f(e.observations):"Sin observaciones";l.innerHTML=`
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
                ${o("Cliente",t)}
                ${o("Personas",n)}
                ${o("Origen",i)}
                ${o("Inicio",y(e.startDate))}
                ${o("Fin estimado",e.estimatedEndDate?y(e.estimatedEndDate):"\u2014")}
                ${o("Observaciones",r)}
            </div>
        </div>`}function o(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function y(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function B(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${f(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${f(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,l.appendChild(t)}function P(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,l.appendChild(t)}var d=0;function T(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),i=document.getElementById("activeOccupationActions");!e||!t||!n||!i||(c=e,O=t,l=n,w=i,c.inert=!0,c.addEventListener("click",r=>{r.target.closest("[data-close-active-occupation]")&&p()}),O.addEventListener("click",p),document.addEventListener("keydown",r=>{r.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&p()}))}async function I(e){if(!e)return;let t=++d;W(),S(),w.classList.add("hidden");try{let n=await x(e);if(t!==d)return;R(n);try{let i=await E(n.occupationId);if(t!==d)return;B(i.accounts)}catch(i){if(t!==d)return;P(i instanceof Error?i.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==d)return;l.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${f(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}var a,A,k,H,v=0,$,s=[],u=null;function M(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function j(){window.clearTimeout($),A.classList.remove("hidden"),a.inert=!1,requestAnimationFrame(()=>a.classList.remove("translate-x-full")),a.setAttribute("aria-hidden","false")}function g(){a.getAttribute("aria-hidden")!=="true"&&(++v,a.classList.add("translate-x-full"),a.setAttribute("aria-hidden","true"),a.inert=!0,$=window.setTimeout(()=>A.classList.add("hidden"),300))}function q(e){let t=s.find(i=>i.id===u)??null,n=s.map(i=>`
        <option value="${i.id}" ${i.id===u?"selected":""}>
            ${M(i.name)}
        </option>
    `).join("")}async function D(e,t){if(!e||!a)return;let n=++v;s=[],u=null,H.textContent=t,j();try{let i=await x(e);if(n!==v)return;let r=await E(i.occupationId);if(n!==v)return;s=r.accounts,u=s.find(m=>m.status==="abierta")?.id??s[0]?.id??null,q(i)}catch(i){if(n!==v)return;let r=i instanceof Error?i.message:"No fue posible consultar la mesa.";k.innerHTML=`<p class="rounded-xl bg-red-50 p-4 text-sm text-red-700">${M(r)}</p>`}}function N(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),i=document.getElementById("orderWorkspaceTitle");!e||!t||!n||!i||(a=e,A=t,k=n,H=i,a.inert=!0,a.addEventListener("click",r=>{r.target.closest("[data-close-order-workspace]")&&g()}),a.addEventListener("change",r=>{let m=r.target;if(!m.matches("[data-order-workspace-account]"))return;u=Number(m.value);let b=s.find(C=>C.id===u),L=a.querySelector("[data-order-workspace-account-info]");b&&L&&(L.textContent=`Estado: ${b.status} \xB7 ${b.invoiceId===null?"Sin orden todav\xEDa":`Orden #${b.invoiceId}`}`)}),A.addEventListener("click",g),document.addEventListener("keydown",r=>{r.key==="Escape"&&a.getAttribute("aria-hidden")==="false"&&g()}))}function K(){T(),N(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),i=n?.closest("[data-table-id]");if(!n||!i)return;let r=Number(i.dataset.resourceId);r&&(n.hasAttribute("data-open-active-detail")?(g(),I(r)):(p(),D(r,i.dataset.tableName??"Mesa")))})}export{K as initTables};
//# sourceMappingURL=tables-B5CUSHZ2.js.map
