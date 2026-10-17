async function v(e){let t=await fetch(`/restaurant/api/occupations/active?resourceId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.occupation)throw new Error(n.message??"No fue posible consultar la atenci\xF3n.");return n.occupation}async function f(e){let t=await fetch(`/restaurant/api/occupations/accounts?occupationId=${encodeURIComponent(e)}`,{headers:{Accept:"application/json"}}),n=await t.json();if(!t.ok||!n.success||!n.data)throw new Error(n.message??"No fue posible consultar las cuentas.");return n.data}var c,u,o,b;function O(){u.classList.remove("hidden"),requestAnimationFrame(()=>{c.classList.remove("translate-x-full")}),c.setAttribute("aria-hidden","false")}function m(){c.classList.add("translate-x-full"),c.setAttribute("aria-hidden","true"),window.setTimeout(()=>{u.classList.add("hidden")},300)}function A(){o.innerHTML=`
        <div class="flex min-h-64 items-center justify-center">
            <div class="flex flex-col items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined animate-spin text-3xl">
                    progress_activity
                </span>
                <span class="text-sm">Cargando atenci\xF3n...</span>
            </div>
        </div>`}function r(e){let t=document.createElement("div");return t.textContent=e,t.innerHTML}function E(e){let t=e.clientName?r(e.clientName):"Sin cliente asociado",n=e.numberOfPeople!==null?String(e.numberOfPeople):"\u2014",a=e.reservationId!==null?`#${e.reservationId}`:"Atenci\xF3n directa",s=e.observations?r(e.observations):"Sin observaciones";o.innerHTML=`
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
                ${i("Cliente",t)}
                ${i("Personas",n)}
                ${i("Origen",a)}
                ${i("Inicio",g(e.startDate))}
                ${i("Fin estimado",e.estimatedEndDate?g(e.estimatedEndDate):"\u2014")}
                ${i("Observaciones",s)}
            </div>
        </div>`}function i(e,t){return`
        <div class="border-b border-slate-100 pb-4">
            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-400">
                ${e}
            </p>
            <p class="text-sm font-medium text-slate-800">
                ${t}
            </p>
        </div>`}function g(e){let t=new Date(e.replace(" ","T"));return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat("es",{dateStyle:"medium",timeStyle:"short"}).format(t)}function y(e){let t=document.createElement("section");t.className="mt-8 border-t border-slate-200 pt-5",t.innerHTML=`
        <h3 class="mb-3 font-semibold text-slate-900">Cuentas</h3>
        ${e.length===0?'<p class="text-sm text-slate-500">Esta atenci\xF3n no tiene cuentas.</p>':e.map(n=>`
                <article class="mb-2 rounded-xl border border-slate-200 p-3">
                    <p class="font-medium text-slate-900">${r(n.name)}</p>
                    <p class="text-xs text-slate-500">
                        ${r(n.status)} \xB7 ${n.invoiceId===null?"Sin orden todav\xEDa":`Orden #${n.invoiceId}`}
                    </p>
                </article>
            `).join("")}`,o.appendChild(t)}function I(e){let t=document.createElement("p");t.className="mt-5 rounded-xl bg-red-50 p-3 text-sm text-red-700",t.textContent=e,o.appendChild(t)}var l=0;function x(){let e=document.getElementById("activeOccupationDrawer"),t=document.getElementById("activeOccupationOverlay"),n=document.getElementById("activeOccupationContent"),a=document.getElementById("activeOccupationActions");!e||!t||!n||!a||(c=e,u=t,o=n,b=a,document.addEventListener("click",async s=>{let d=s.target,p=d.closest("[data-active-occupation]");p&&d.classList.contains("paneldetalle")&&L(p)}),c.addEventListener("click",s=>{s.target.closest("[data-close-active-occupation]")&&m()}),u.addEventListener("click",m),document.addEventListener("keydown",s=>{s.key==="Escape"&&c.getAttribute("aria-hidden")==="false"&&m()}))}async function L(e){let t=Number(e.dataset.resourceId);if(!t)return;let n=++l;O(),A(),b.classList.add("hidden");try{let a=await v(t);if(n!==l)return;E(a);try{let s=await f(a.occupationId);if(n!==l)return;y(s.accounts)}catch(s){if(n!==l)return;I(s instanceof Error?s.message:"No fue posible consultar las cuentas.")}}catch(a){o.innerHTML=`
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                ${r(a instanceof Error?a.message:"Ocurri\xF3 un error inesperado.")}
            </div>`}}function D(){console.log("Restaurant \u2192 Tables cargado x"),x()}export{D as initTables};
//# sourceMappingURL=tables-UVJ272WA.js.map
