async function p(e){let t=await fetch(`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.occupation)throw new Error(n.message??"No fue posible consultar la atenci\xF3n.");return n.occupation}async function v(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var r,d,i,g,x;function O(){window.clearTimeout(x),d.classList.remove("hidden"),requestAnimationFrame(()=>{r.classList.remove("translate-x-full")}),r.setAttribute("aria-hidden","false")}function A(){i.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function o(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function E(e){let t=e.clientName?o(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",a=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",s=e.observations?o(e.observations):"Sin observaciones";i.innerHTML=`
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
                ${c("Inicio",f(e.startDate))}
                ${c("Fin estimado",e.estimatedEndDate?f(e.estimatedEndDate):"\u2014")}
                ${c("Observaciones",s)}
            </div>
        </div>`}function c(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function f(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function w(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${o(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${o(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,i.appendChild(t)}function y(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,i.appendChild(t)}var l=0;function b(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),a=document.getElementById("activeOccupationActions");!e||!t||!n||!a||(r=e,d=t,i=n,g=a,document.addEventListener("click",async s=>{let u=s.target,m=u.closest("[data-active-occupation]");m&&u.classList.contains("paneldetalle")&&h(m)}),r.addEventListener("click",s=>{s.target.closest("[data-close-active-occupation]")&&closeDrawer()}),d.addEventListener("click",closeDrawer),document.addEventListener("keydown",s=>{s.key==="Escape"&&r.getAttribute("aria-hidden")==="false"&&closeDrawer()}))}async function h(e){let t=Number(e.dataset.resourceId);if(!t)return;let n=++l;O(),A(),g.classList.add("hidden");try{let a=await p(t);if(n!==l)return;E(a);try{let s=await v(a.occupationId);if(n!==l)return;w(s.accounts)}catch(s){if(n!==l)return;y(s instanceof Error?s.message:"No fue posible consultar las cuentas.")}}catch(a){i.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${o(a instanceof Error?a.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function $(){console.log("Restaurant \u2192 Tables cargado x"),b()}export{$ as initTables};
//# sourceMappingURL=tables-OFTMPKNB.js.map
