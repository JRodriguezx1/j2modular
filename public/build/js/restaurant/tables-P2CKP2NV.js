async function g(e){let t=await fetch(`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.occupation)throw new Error(n.message??"No fue posible consultar la atenci\xF3n.");return n.occupation}async function E(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var o,O,l,T,I;function W(){window.clearTimeout(I),O.classList.remove("hidden"),o.inert=!1,requestAnimationFrame(()=>{o.classList.remove("translate-x-full")}),o.setAttribute("aria-hidden","false")}function p(){o.getAttribute("aria-hidden")!=="true"&&(++d,o.classList.add("translate-x-full"),o.setAttribute("aria-hidden","true"),o.inert=!0,I=window.setTimeout(()=>{O.classList.add("hidden")},300))}function S(){l.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function v(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function q(e){let t=e.clientName?v(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",s=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",a=e.observations?v(e.observations):"Sin observaciones";l.innerHTML=`
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
                ${c("Origen",s)}
                ${c("Inicio",L(e.startDate))}
                ${c("Fin estimado",e.estimatedEndDate?L(e.estimatedEndDate):"\u2014")}
                ${c("Observaciones",a)}
            </div>
        </div>`}function c(e,t){return`
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
                    <p class="font-medium text-slate-900">${v(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${v(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,l.appendChild(t)}function B(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,l.appendChild(t)}var d=0;function k(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),s=document.getElementById("activeOccupationActions");!e||!t||!n||!s||(o=e,O=t,l=n,T=s,o.inert=!0,o.addEventListener("click",a=>{a.target.closest("[data-close-active-occupation]")&&p()}),O.addEventListener("click",p),document.addEventListener("keydown",a=>{a.key==="Escape"&&o.getAttribute("aria-hidden")==="false"&&p()}))}async function $(e){if(!e)return;let t=++d;W(),S(),T.classList.add("hidden");try{let n=await g(e);if(t!==d)return;q(n);try{let s=await E(n.occupationId);if(t!==d)return;R(s.accounts)}catch(s){if(t!==d)return;B(s instanceof Error?s.message:"No fue posible consultar las cuentas.")}}catch(n){if(t!==d)return;l.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${v(n instanceof Error?n.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}var r,h,w,H,f=0,M,i=[],u=null;function A(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function P(){window.clearTimeout(M),h.classList.remove("hidden"),r.inert=!1,requestAnimationFrame(()=>r.classList.remove("translate-x-full")),r.setAttribute("aria-hidden","false")}function b(){++f,r.classList.add("translate-x-full"),r.setAttribute("aria-hidden","true"),r.inert=!0,M=window.setTimeout(()=>h.classList.add("hidden"),300)}function j(e){let t=i.find(s=>s.id===u)??null,n=i.map(s=>`
        <option value="${s.id}" ${s.id===u?"selected":""}>
            ${A(s.name)}
        </option>
    `).join("");w.innerHTML=`
        <div class="mb-5 rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-600">
            Atenci\xF3n #${e.occupationId} \xB7 Estado: ${A(e.status)}
        </div>
        <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
            <section class="min-h-[320px] rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-lg font-semibold text-slate-900">Cat\xE1logo de productos</h3>
                <p class="mt-3 text-sm text-slate-500">
                    Aqu\xED se incorporar\xE1n la b\xFAsqueda, las categor\xEDas y los filtros de productos.
                </p>
            </section>
            <section class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-lg font-semibold text-slate-900">Cuenta y carrito</h3>
                ${i.length>0?`
                    <label for="orderWorkspaceAccount" class="mt-5 block text-sm font-medium text-slate-700">Cuenta de destino</label>
                    <select id="orderWorkspaceAccount" data-order-workspace-account
                        class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
                        ${n}
                    </select>
                    <p data-order-workspace-account-info class="mt-3 text-sm text-slate-500">
                        ${t?`Estado: ${A(t.status)} \xB7 ${t.invoiceId===null?"Sin orden todav\xEDa":`Orden #${t.invoiceId}`}`:""}
                    </p>
                `:'<p class="mt-5 text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>'}
                <div class="mt-6 border-t border-slate-200 pt-5 text-sm text-slate-500">
                    El carrito y las acciones de venta se incorporar\xE1n en el siguiente paso.
                </div>
            </section>
        </div>`}async function C(e,t){if(!e||!r)return;let n=++f;i=[],u=null,H.textContent=t,w.innerHTML='<p class="text-sm text-slate-500">Cargando atenci\xF3n y cuentas...</p>',P();try{let s=await g(e);if(n!==f)return;let a=await E(s.occupationId);if(n!==f)return;i=a.accounts,u=i.find(m=>m.status==="abierta")?.id??i[0]?.id??null,j(s)}catch(s){if(n!==f)return;let a=s instanceof Error?s.message:"No fue posible consultar la mesa.";w.innerHTML=`<p class="rounded-xl bg-red-50 p-4 text-sm text-red-700">${A(a)}</p>`}}function D(){let e=document.getElementById("orderWorkspaceDrawer"),t=document.getElementById("orderWorkspaceOverlay"),n=document.getElementById("orderWorkspaceContent"),s=document.getElementById("orderWorkspaceTitle");!e||!t||!n||!s||(r=e,h=t,w=n,H=s,r.inert=!0,r.addEventListener("click",a=>{a.target.closest("[data-close-order-workspace]")&&b()}),r.addEventListener("change",a=>{let m=a.target;if(!m.matches("[data-order-workspace-account]"))return;u=Number(m.value);let x=i.find(N=>N.id===u),y=r.querySelector("[data-order-workspace-account-info]");x&&y&&(y.textContent=`Estado: ${x.status} \xB7 ${x.invoiceId===null?"Sin orden todav\xEDa":`Orden #${x.invoiceId}`}`)}),h.addEventListener("click",b),document.addEventListener("keydown",a=>{a.key==="Escape"&&r.getAttribute("aria-hidden")==="false"&&b()}))}function K(){k(),D(),document.addEventListener("click",e=>{let n=e.target.closest("[data-open-active-detail], [data-open-order-workspace]"),s=n?.closest("[data-table-id]");if(!n||!s||s.dataset.status!=="ocupada")return;let a=Number(s.dataset.resourceId);a&&(n.hasAttribute("data-open-active-detail")?(b(),$(a)):(p(),C(a,s.dataset.tableName??"Mesa")))})}export{K as initTables};
//# sourceMappingURL=tables-P2CKP2NV.js.map
