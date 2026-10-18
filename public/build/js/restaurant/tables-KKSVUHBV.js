async function E(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),i=await n.json();if(!n.ok||!i.success||!i.occupation)throw new Error(i.message??"No fue posible consultar la atenci\xF3n.");return i.occupation}async function x(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var o,O,u,h,T;function S(){window.clearTimeout(T),O.classList.remove("hidden"),o.inert=!1,requestAnimationFrame(()=>{o.classList.remove("translate-x-full")}),o.setAttribute("aria-hidden","false")}function p(){o.getAttribute("aria-hidden")!=="true"&&(++l,o.classList.add("translate-x-full"),o.setAttribute("aria-hidden","true"),o.inert=!0,T=window.setTimeout(()=>{O.classList.add("hidden")},300))}function W(){u.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function v(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function q(e){let t=e.clientName?v(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",i=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",c=e.observations?v(e.observations):"Sin observaciones";u.innerHTML=`
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
                ${d("Inicio",w(e.startDate))}
                ${d("Fin estimado",e.estimatedEndDate?w(e.estimatedEndDate):"\u2014")}
                ${d("Observaciones",c)}
            </div>
        </div>`}function d(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function w(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function R(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${v(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${v(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,u.appendChild(t)}function B(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,u.appendChild(t)}var l=0;function I(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),i=document.getElementById("activeOccupationActions");!e||!t||!n||!i||(o=e,O=t,u=n,h=i,o.inert=!0,o.addEventListener("click",c=>{c.target.closest("[data-close-active-occupation]")&&p()}),O.addEventListener("click",p),document.addEventListener("keydown",c=>{c.key==="Escape"&&o.getAttribute("aria-hidden")==="false"&&p()}))}async function k(e){if(!e)return;let t=++l;S(),W(),h.classList.add("hidden");try{let n=await E(e);if(t!==l)return;q(n);try{let i=await x(n.occupationId);if(t!==l)return;R(i.accounts)}catch(i){if(t!==l)return;B(i instanceof Error?i.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==l)return;u.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${v(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}var a,A,P,M,f=0,H,s=[],m=null;function $(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function j(){window.clearTimeout(H),A.classList.remove("hidden"),a.inert=!1,a.setAttribute("aria-hidden","false"),requestAnimationFrame(()=>a.classList.remove("translate-x-full"))}function g(){if(a.getAttribute("aria-hidden")==="true")return;++f;let e=document.activeElement;e instanceof HTMLElement&&a.contains(e)&&e.blur(),a.classList.add("translate-x-full"),a.inert=!0,a.setAttribute("aria-hidden","true"),H=window.setTimeout(()=>A.classList.add("hidden"),300)}function F(e){let t=s.find(i=>i.id===m)??null,n=s.map(i=>`
        <option value="${i.id}" ${i.id===m?"selected":""}>
            ${$(i.name)}
        </option>
    `).join("")}async function D(e,t){if(!e||!a)return;let n=++f;s=[],m=null,M.textContent=t,j();try{let i=await E(e);if(n!==f)return;let c=await x(i.occupationId);if(n!==f)return;s=c.accounts,m=s.find(r=>r.status==="abierta")?.id??s[0]?.id??null,document.querySelector("#atencionNoActiva").textContent="",F(i)}catch(i){if(n!==f)return;let c=i instanceof Error?i.message:"No fue posible consultar la mesa.";document.querySelector("#atencionNoActiva").textContent=`${$(c)}`}}function N(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),i=document.getElementById("orderWorkspaceTitle"),c=document.querySelector("#atencionTextMsg");!e||!t||!n||!i||!c||(a=e,A=t,P=n,M=i,a.inert=!0,a.addEventListener("click",r=>{r.target.closest("[data-close-order-workspace]")&&g()}),a.addEventListener("change",r=>{let y=r.target;if(!y.matches("[data-order-workspace-account]"))return;m=Number(y.value);let b=s.find(C=>C.id===m),L=a.querySelector("[data-order-workspace-account-info]");b&&L&&(L.textContent=`Estado: ${b.status} \xB7 ${b.invoiceId===null?"Sin orden todav\xEDa":`Orden #${b.invoiceId}`}`)}),A.addEventListener("click",g),document.addEventListener("keydown",r=>{r.key==="Escape"&&a.getAttribute("aria-hidden")==="false"&&g()}))}function Q(){I(),N(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),i=n?.closest("[data-table-id]");if(!n||!i)return;let c=Number(i.dataset.resourceId);c&&(n.hasAttribute("data-open-active-detail")?(g(),k(c)):(p(),D(c,i.dataset.tableName??"Mesa")))})}export{Q as initTables};
//# sourceMappingURL=tables-KKSVUHBV.js.map
