async function E(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),i=await n.json();if(!n.ok||!i.success||!i.occupation)throw new Error(i.message??"No fue posible consultar la atenci\xF3n.");return i.occupation}async function x(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var r,O,u,T,I;function W(){window.clearTimeout(I),O.classList.remove("hidden"),r.inert=!1,requestAnimationFrame(()=>{r.classList.remove("translate-x-full")}),r.setAttribute("aria-hidden","false")}function p(){r.getAttribute("aria-hidden")!=="true"&&(++l,r.classList.add("translate-x-full"),r.setAttribute("aria-hidden","true"),r.inert=!0,I=window.setTimeout(()=>{O.classList.add("hidden")},300))}function R(){u.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function f(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function q(e){let t=e.clientName?f(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",i=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",a=e.observations?f(e.observations):"Sin observaciones";u.innerHTML=`
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
                ${d("Cliente",t)}
                ${d("Personas",n)}
                ${d("Origen",i)}
                ${d("Inicio",h(e.startDate))}
                ${d("Fin estimado",e.estimatedEndDate?h(e.estimatedEndDate):"\u2014")}
                ${d("Observaciones",a)}
            </div>
        </div>`}function d(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function h(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function B(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${f(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${f(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,u.appendChild(t)}function P(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,u.appendChild(t)}var l=0;function k(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),i=document.getElementById("activeOccupationActions");!e||!t||!n||!i||(r=e,O=t,u=n,T=i,r.inert=!0,r.addEventListener("click",a=>{a.target.closest("[data-close-active-occupation]")&&p()}),O.addEventListener("click",p),document.addEventListener("keydown",a=>{a.key==="Escape"&&r.getAttribute("aria-hidden")==="false"&&p()}))}async function M(e){if(!e)return;let t=++l;W(),R(),T.classList.add("hidden");try{let n=await E(e);if(t!==l)return;q(n);try{let i=await x(n.occupationId);if(t!==l)return;B(i.accounts)}catch(i){if(t!==l)return;P(i instanceof Error?i.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==l)return;u.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${f(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}var c,L,j,H,v=0,$,s=[],m=null,A=document.querySelector("#atencionMesaText");function D(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function F(){window.clearTimeout($),L.classList.remove("hidden"),c.inert=!1,c.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>c.classList.remove("translate-x-full"))}function g(){if(c.getAttribute("aria-hidden")==="true")return;++v;let e=document.activeElement;e instanceof HTMLElement&&c.contains(e)&&e.blur(),c.classList.add("translate-x-full"),c.inert=!0,c.setAttribute("aria-hidden","true"),$=window.setTimeout(()=>L.classList.add("hidden"),300)}function U(e){let t=s.find(i=>i.id===m)??null,n=s.map(i=>`
        <option value="${i.id}" ${i.id===m?"selected":""}>
            ${D(i.name)}
        </option>
    `).join("")}async function C(e,t){if(!e||!c)return;let n=++v;s=[],m=null,H.textContent=t,F();try{let i=await E(e);if(n!==v)return;let a=await x(i.occupationId);if(n!==v)return;s=a.accounts,m=s.find(o=>o.status==="abierta")?.id??s[0]?.id??null,A.classList.add("bg-emerald-50","text-emerald-700"),A.textContent="En atenci\xF3n",U(i)}catch(i){if(n!==v)return;let a=i instanceof Error?i.message:"No fue posible consultar la mesa.";A.classList.add("bg-red-50","text-red-700"),A.textContent=`${D(a)}`}}function N(){let e=window.innerWidth;console.log(e);let t=document.getElementById("orderWorkspaceDrawer"),n=document.getElementById("orderWorkspaceOverlay"),i=document.getElementById("orderWorkspaceContent"),a=document.getElementById("orderWorkspaceTitle");!t||!n||!i||!a||(c=t,L=n,j=i,H=a,c.inert=!0,c.addEventListener("click",o=>{o.target.closest("[data-close-order-workspace]")&&g()}),c.addEventListener("change",o=>{let w=o.target;if(!w.matches("[data-order-workspace-account]"))return;m=Number(w.value);let b=s.find(S=>S.id===m),y=c.querySelector("[data-order-workspace-account-info]");b&&y&&(y.textContent=`Estado: ${b.status} \xB7 ${b.invoiceId===null?"Sin orden todav\xEDa":`Orden #${b.invoiceId}`}`)}),L.addEventListener("click",g),document.addEventListener("keydown",o=>{o.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&g()}))}function X(){k(),N(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),i=n?.closest("[data-table-id]");if(!n||!i)return;let a=Number(i.dataset.resourceId);a&&(n.hasAttribute("data-open-active-detail")?(g(),M(a)):(p(),C(a,i.dataset.tableName??"Mesa")))})}export{X as initTables};
//# sourceMappingURL=tables-W7XBCJP2.js.map
