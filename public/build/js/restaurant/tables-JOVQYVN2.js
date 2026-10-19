async function x(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),i=await n.json();if(!n.ok||!i.success||!i.occupation)throw new Error(i.message??"No fue posible consultar la atenci\xF3n.");return i.occupation}async function E(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var o,A,u,h,T;function W(){window.clearTimeout(T),A.classList.remove("hidden"),o.inert=!1,requestAnimationFrame(()=>{o.classList.remove("translate-x-full")}),o.setAttribute("aria-hidden","false")}function p(){o.getAttribute("aria-hidden")!=="true"&&(++d,o.classList.add("translate-x-full"),o.setAttribute("aria-hidden","true"),o.inert=!0,T=window.setTimeout(()=>{A.classList.add("hidden")},300))}function P(){u.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function f(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function R(e){let t=e.clientName?f(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",i=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",a=e.observations?f(e.observations):"Sin observaciones";u.innerHTML=`
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
                ${l("Cliente",t)}
                ${l("Personas",n)}
                ${l("Origen",i)}
                ${l("Inicio",w(e.startDate))}
                ${l("Fin estimado",e.estimatedEndDate?w(e.estimatedEndDate):"\u2014")}
                ${l("Observaciones",a)}
            </div>
        </div>`}function l(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function w(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function j(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${f(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${f(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,u.appendChild(t)}function q(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,u.appendChild(t)}var d=0;function I(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),i=document.getElementById("activeOccupationActions");!e||!t||!n||!i||(o=e,A=t,u=n,h=i,o.inert=!0,o.addEventListener("click",a=>{a.target.closest("[data-close-active-occupation]")&&p()}),A.addEventListener("click",p),document.addEventListener("keydown",a=>{a.key==="Escape"&&o.getAttribute("aria-hidden")==="false"&&p()}))}async function k(e){if(!e)return;let t=++d;W(),P(),h.classList.add("hidden");try{let n=await x(e);if(t!==d)return;R(n);try{let i=await E(n.occupationId);if(t!==d)return;j(i.accounts)}catch(i){if(t!==d)return;q(i instanceof Error?i.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==d)return;u.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${f(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}async function M(){return await(await fetch("/admin/api/inventory/getAllcategorias")).json()}var r,L,B,H,v=0,$,c=[],m=null,O=document.querySelector("#atencionMesaText");function C(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function F(){window.clearTimeout($),L.classList.remove("hidden"),r.inert=!1,r.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>r.classList.remove("translate-x-full"))}function g(){if(r.getAttribute("aria-hidden")==="true")return;++v;let e=document.activeElement;e instanceof HTMLElement&&r.contains(e)&&e.blur(),r.classList.add("translate-x-full"),r.inert=!0,r.setAttribute("aria-hidden","true"),$=window.setTimeout(()=>L.classList.add("hidden"),300)}function U(e){let t=c.find(i=>i.id===m)??null,n=c.map(i=>`
        <option value="${i.id}" ${i.id===m?"selected":""}>
            ${C(i.name)}
        </option>
    `).join("")}async function D(e,t){if(!e||!r)return;let n=++v;c=[],m=null,H.textContent=t,F();try{let i=await x(e);if(n!==v)return;let a=await E(i.occupationId);if(n!==v)return;c=a.accounts,m=c.find(s=>s.status==="abierta")?.id??c[0]?.id??null,O.classList.add("bg-emerald-50","text-emerald-700"),O.textContent="En atenci\xF3n",U(i)}catch(i){if(n!==v)return;let a=i instanceof Error?i.message:"No fue posible consultar la mesa.";O.classList.add("bg-red-50","text-red-700"),O.textContent=`${C(a)}`}}function N(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),i=document.getElementById("orderWorkspaceTitle");!e||!t||!n||!i||(r=e,L=t,B=n,H=i,r.inert=!0,r.addEventListener("click",a=>{a.target.closest("[data-close-order-workspace]")&&g()}),r.addEventListener("change",a=>{let s=a.target;if(!s.matches("[data-order-workspace-account]"))return;m=Number(s.value);let b=c.find(S=>S.id===m),y=r.querySelector("[data-order-workspace-account-info]");b&&y&&(y.textContent=`Estado: ${b.status} \xB7 ${b.invoiceId===null?"Sin orden todav\xEDa":`Orden #${b.invoiceId}`}`)}),L.addEventListener("click",g),document.addEventListener("keydown",a=>{a.key==="Escape"&&r.getAttribute("aria-hidden")==="false"&&g()}))}function Z(){I(),N();let e=M();console.log(e),document.addEventListener("click",t=>{let i=t.target.closest("[data-open-active-detail], [data-open-order-workspace]"),a=i?.closest("[data-table-id]");if(!i||!a)return;let s=Number(a.dataset.resourceId);s&&(i.hasAttribute("data-open-active-detail")?(g(),k(s)):(p(),D(s,a.dataset.tableName??"Mesa")))})}export{Z as initTables};
//# sourceMappingURL=tables-JOVQYVN2.js.map
