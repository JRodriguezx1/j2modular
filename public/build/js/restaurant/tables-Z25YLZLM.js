async function b(e){let t=`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,n=await fetch(t,{headers:{Accept:"application/json"}}),i=await n.json();if(!n.ok||!i.success||!i.occupation)throw new Error(i.message??"No fue posible consultar la atenci\xF3n.");return i.occupation}async function E(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var c,x,d,w,h;function N(){window.clearTimeout(h),x.classList.remove("hidden"),c.inert=!1,requestAnimationFrame(()=>{c.classList.remove("translate-x-full")}),c.setAttribute("aria-hidden","false")}function u(){c.getAttribute("aria-hidden")!=="true"&&(++o,c.classList.add("translate-x-full"),c.setAttribute("aria-hidden","true"),c.inert=!0,h=window.setTimeout(()=>{x.classList.add("hidden")},300))}function W(){d.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function m(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function S(e){let t=e.clientName?m(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",i=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",a=e.observations?m(e.observations):"Sin observaciones";d.innerHTML=`
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
        </div>`}function L(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function R(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${m(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${m(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,d.appendChild(t)}function B(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,d.appendChild(t)}var o=0;function T(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),i=document.getElementById("activeOccupationActions");!e||!t||!n||!i||(c=e,x=t,d=n,w=i,c.inert=!0,c.addEventListener("click",a=>{a.target.closest("[data-close-active-occupation]")&&u()}),x.addEventListener("click",u),document.addEventListener("keydown",a=>{a.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&u()}))}async function I(e){if(!e)return;let t=++o;N(),W(),w.classList.add("hidden");try{let n=await b(e);if(t!==o)return;S(n);try{let i=await E(n.occupationId);if(t!==o)return;R(i.accounts)}catch(i){if(t!==o)return;B(i instanceof Error?i.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==o)return;d.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${m(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}var r,O,k,H,p=0,M,f=[],A=null;function P(){window.clearTimeout(M),O.classList.remove("hidden"),r.inert=!1,requestAnimationFrame(()=>r.classList.remove("translate-x-full")),r.setAttribute("aria-hidden","false")}function v(){r.getAttribute("aria-hidden")!=="true"&&(++p,r.classList.add("translate-x-full"),r.setAttribute("aria-hidden","true"),r.inert=!0,M=window.setTimeout(()=>O.classList.add("hidden"),300))}async function $(e,t){if(!e||!r)return;let n=++p;f=[],A=null,H.textContent=t,k.innerHTML='<p class="text-sm text-slate-500">Cargando atenci\xF3n y cuentas...</p>',P();try{let i=await b(e);if(n!==p)return;let a=await E(i.occupationId);if(n!==p)return;f=a.accounts,A=f.find(l=>l.status==="abierta")?.id??f[0]?.id??null,console.log(23)}catch(i){if(n!==p)return;let a=i instanceof Error?i.message:"No fue posible consultar la mesa."}}function D(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),i=document.getElementById("orderWorkspaceTitle");!e||!t||!n||!i||(r=e,O=t,k=n,H=i,r.inert=!0,r.addEventListener("click",a=>{a.target.closest("[data-close-order-workspace]")&&v()}),r.addEventListener("change",a=>{let l=a.target;if(!l.matches("[data-order-workspace-account]"))return;A=Number(l.value);let g=f.find(C=>C.id===A),y=r.querySelector("[data-order-workspace-account-info]");g&&y&&(y.textContent=`Estado: ${g.status} \xB7 ${g.invoiceId===null?"Sin orden todav\xEDa":`Orden #${g.invoiceId}`}`)}),O.addEventListener("click",v),document.addEventListener("keydown",a=>{a.key==="Escape"&&r.getAttribute("aria-hidden")==="false"&&v()}))}function G(){T(),D(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),i=n?.closest("[data-table-id]");if(!n||!i)return;let a=Number(i.dataset.resourceId);a&&(n.hasAttribute("data-open-active-detail")?(v(),I(a)):(u(),$(a,i.dataset.tableName??"Mesa")))})}export{G as initTables};
//# sourceMappingURL=tables-Z25YLZLM.js.map
